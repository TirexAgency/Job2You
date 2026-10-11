<?php

namespace Tests\Feature\Scraping;

use App\DTO\RawOfferDTO;
use App\Models\Source;
use App\Services\Scraping\Collectors\RssCollector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RssCollectorTest extends TestCase
{
    use RefreshDatabase;

    protected RssCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collector = new RssCollector;
    }

    public function test_collector_key_is_rss(): void
    {
        $this->assertSame('rss', RssCollector::getCollectorKey());
    }

    public function test_fingerprint_generation_is_consistent(): void
    {
        $data = [
            'title' => 'Développeur PHP',
            'company' => 'TechCorp',
            'location' => 'Paris',
            'contract_type' => 'CDI',
        ];

        $fp1 = $this->collector->fingerprint($data);
        $fp2 = $this->collector->fingerprint($data);

        $this->assertSame($fp1, $fp2);
        $this->assertEquals(64, strlen($fp1)); // SHA256 hex
    }

    public function test_fingerprint_changes_with_different_data(): void
    {
        $base = [
            'title' => 'Développeur PHP',
            'company' => 'TechCorp',
            'location' => 'Paris',
            'contract_type' => 'CDI',
        ];

        $fp1 = $this->collector->fingerprint($base);
        $fp2 = $this->collector->fingerprint(array_merge($base, ['title' => 'Développeur Laravel']));

        $this->assertNotSame($fp1, $fp2);
    }

    public function test_normalize_cleans_title(): void
    {
        $raw = new RawOfferDTO(
            externalId: '1',
            title: '  Offre: Développeur PHP (H/F)  ',
            company: 'TechCorp',
            location: 'Paris',
            contractType: 'CDI',
        );

        $source = Source::factory()->create(['collector_key' => 'rss']);
        $normalized = $this->collector->normalize($raw, $source);

        $this->assertSame('Développeur PHP', $normalized['title']);
    }

    public function test_normalize_contract_type(): void
    {
        $testCases = [
            'cdi' => 'CDI',
            'CDI' => 'CDI',
            'cdd' => 'CDD',
            'freelance' => 'Freelance',
            'stage' => 'Stage',
            'internship' => 'Stage',
            'alternance' => 'Alternance',
            'temps partiel' => 'Temps partiel',
            'part-time' => 'Temps partiel',
            'unknown' => 'Unknown',
        ];

        $source = Source::factory()->create(['collector_key' => 'rss']);

        foreach ($testCases as $input => $expected) {
            $raw = new RawOfferDTO(
                externalId: '1',
                title: 'Test',
                contractType: $input,
            );
            $normalized = $this->collector->normalize($raw, $source);
            $this->assertSame($expected, $normalized['contract_type'], "Failed for input: $input");
        }
    }

    public function test_normalize_location_deduplicates(): void
    {
        $raw = new RawOfferDTO(
            externalId: '1',
            title: 'Test',
            location: 'Paris, Paris, 75000',
        );

        $source = Source::factory()->create(['collector_key' => 'rss']);
        $normalized = $this->collector->normalize($raw, $source);

        $this->assertSame('Paris, 75000', $normalized['location']);
    }

    public function test_collect_returns_empty_when_no_base_url(): void
    {
        $source = Source::factory()->create(['base_url' => null, 'collector_key' => 'rss']);
        $offers = $this->collector->collect($source);

        $this->assertEmpty($offers);
    }

    public function test_collect_parses_rss_feed(): void
    {
        $rssXml = <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0">
            <channel>
                <title>Job Feed</title>
                <item>
                    <title>Développeur Laravel</title>
                    <link>https://example.com/job/1</link>
                    <guid>https://example.com/job/1</guid>
                    <description><![CDATA[<p>Description du poste</p>]]></description>
                    <pubDate>Mon, 01 Jan 2026 10:00:00 +0000</pubDate>
                    <author>TechCorp</author>
                    <category>IT</category>
                </item>
                <item>
                    <title>Développeur Symfony</title>
                    <link>https://example.com/job/2</link>
                    <guid>https://example.com/job/2</guid>
                    <description>Description simple</description>
                    <pubDate>Tue, 02 Jan 2026 10:00:00 +0000</pubDate>
                </item>
            </channel>
        </rss>
        XML;

        Http::fake([
            'https://example.com/feed.rss' => Http::response($rssXml, 200, ['Content-Type' => 'application/rss+xml']),
        ]);

        $source = Source::factory()->create([
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss',
        ]);

        $offers = $this->collector->collect($source);

        $this->assertCount(2, $offers);
        $this->assertSame('Développeur Laravel', $offers[0]->title);
        $this->assertSame('TechCorp', $offers[0]->company);
        $this->assertSame('Développeur Symfony', $offers[1]->title);
    }

    public function test_collect_handles_content_encoded(): void
    {
        $rssXml = <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
            <channel>
                <title>Job Feed</title>
                <item>
                    <title>Développeur Full Stack</title>
                    <link>https://example.com/job/3</link>
                    <guid>https://example.com/job/3</guid>
                    <content:encoded><![CDATA[<div>Description complète avec <strong>HTML</strong></div>]]></content:encoded>
                    <pubDate>Wed, 03 Jan 2026 10:00:00 +0000</pubDate>
                </item>
            </channel>
        </rss>
        XML;

        Http::fake([
            'https://example.com/feed.rss' => Http::response($rssXml, 200, ['Content-Type' => 'application/rss+xml']),
        ]);

        $source = Source::factory()->create([
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss',
        ]);

        $offers = $this->collector->collect($source);

        $this->assertCount(1, $offers);
        $this->assertStringContainsString('Description complète', $offers[0]->description);
        $this->assertStringNotContainsString('<div>', $offers[0]->description);
        $this->assertStringNotContainsString('<strong>', $offers[0]->description);
    }
}
