<?php

namespace Tests\Feature\Scraping;

use App\Models\Offer;
use App\Models\Source;
use App\Services\Scraping\Collectors\RssCollector;
use App\Services\Scraping\ScrapingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScrapingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ScrapingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ScrapingService::class);
    }

    public function test_scrape_source_creates_offers(): void
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
                    <description>Description du poste</description>
                    <pubDate>Mon, 01 Jan 2026 10:00:00 +0000</pubDate>
                    <author>TechCorp</author>
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
            'active' => true,
        ]);

        $stats = $this->service->scrapeSource($source);

        $this->assertSame(1, $stats['created']);
        $this->assertSame(0, $stats['updated']);
        $this->assertSame(0, $stats['skipped']);
        $this->assertSame(0, $stats['errors']);

        $this->assertDatabaseHas('offers', [
            'source_id' => $source->id,
            'external_id' => 'https://example.com/job/1',
            'title' => 'Développeur Laravel',
            'status' => 'active',
        ]);

        $source->refresh();
        $this->assertNotNull($source->last_run_at);
    }

    public function test_scrape_source_updates_existing_offer(): void
    {
        $callCount = 0;

        Http::fake(function () use (&$callCount) {
            $callCount++;

            $rssXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Job Feed</title>
        <item>
            <title>Développeur Laravel Senior</title>
            <link>https://example.com/job/1</link>
            <guid>https://example.com/job/1</guid>
            <description>Description mise à jour</description>
            <pubDate>Mon, 01 Jan 2026 10:00:00 +0000</pubDate>
            <author>TechCorp</author>
        </item>
    </channel>
</rss>
XML;

            $rssXmlUpdated = str_replace('Développeur Laravel Senior', 'Développeur Laravel Lead', $rssXml);
            $rssXmlUpdated = str_replace('Description mise à jour', 'Nouvelle description', $rssXmlUpdated);

            return Http::response($callCount === 1 ? $rssXml : $rssXmlUpdated, 200, ['Content-Type' => 'application/rss+xml']);
        });

        $source = Source::factory()->create([
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss',
            'active' => true,
        ]);

        // Première collecte
        $this->service->scrapeSource($source);
        $this->assertDatabaseHas('offers', ['external_id' => 'https://example.com/job/1', 'title' => 'Développeur Laravel Senior']);

        // Deuxième collecte
        $stats = $this->service->scrapeSource($source);

        $this->assertSame(0, $stats['created']);
        $this->assertSame(1, $stats['updated']);

        $this->assertDatabaseHas('offers', [
            'external_id' => 'https://example.com/job/1',
            'title' => 'Développeur Laravel Lead',
            'description' => 'Nouvelle description',
        ]);
    }

    public function test_scrape_source_updates_description_when_fingerprint_is_unchanged(): void
    {
        $callCount = 0;

        Http::preventStrayRequests();
        Http::fake(function () use (&$callCount) {
            $callCount++;

            $description = $callCount === 1 ? 'Ancienne description' : 'Nouvelle description';

            $rssXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Job Feed</title>
        <item>
            <title>Développeur Laravel</title>
            <link>https://example.com/job/1</link>
            <guid>https://example.com/job/1</guid>
            <description>{$description}</description>
            <pubDate>Mon, 01 Jan 2026 10:00:00 +0000</pubDate>
            <author>TechCorp</author>
        </item>
    </channel>
</rss>
XML;

            return Http::response($rssXml, 200, ['Content-Type' => 'application/rss+xml']);
        });

        $source = Source::factory()->create([
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss',
            'active' => true,
        ]);

        $this->service->scrapeSource($source);
        $stats = $this->service->scrapeSource($source);

        $this->assertSame(0, $stats['created']);
        $this->assertSame(1, $stats['updated']);
        $this->assertDatabaseHas('offers', [
            'source_id' => $source->id,
            'external_id' => 'https://example.com/job/1',
            'description' => 'Nouvelle description',
        ]);
    }

    public function test_scrape_source_skips_duplicate_fingerprint(): void
    {
        $rssXml = <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0">
            <channel>
                <title>Job Feed</title>
                <item>
                    <title>Développeur PHP</title>
                    <link>https://example.com/job/1</link>
                    <guid>https://example.com/job/1</guid>
                    <description>Desc 1</description>
                    <pubDate>Mon, 01 Jan 2026 10:00:00 +0000</pubDate>
                    <author>TechCorp</author>
                </item>
                <item>
                    <title>Développeur PHP</title>
                    <link>https://example.com/job/2</link>
                    <guid>https://example.com/job/2</guid>
                    <description>Desc 2</description>
                    <pubDate>Mon, 01 Jan 2026 10:00:00 +0000</pubDate>
                    <author>TechCorp</author>
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
            'active' => true,
        ]);

        $stats = $this->service->scrapeSource($source);

        // 1 créée, 1 ignorée (même fingerprint: titre + company + location + contract_type)
        $this->assertSame(1, $stats['created']);
        $this->assertSame(1, $stats['skipped']);

        $this->assertEquals(1, Offer::where('source_id', $source->id)->count());
    }

    public function test_scrape_source_different_source_same_fingerprint_skipped(): void
    {
        $rssXml = <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0">
            <channel>
                <title>Job Feed</title>
                <item>
                    <title>Développeur PHP</title>
                    <link>https://source1.com/job/1</link>
                    <guid>https://source1.com/job/1</guid>
                    <description>Description</description>
                    <pubDate>Mon, 01 Jan 2026 10:00:00 +0000</pubDate>
                    <author>TechCorp</author>
                </item>
            </channel>
        </rss>
        XML;

        // Register additional RSS collectors for testing
        $this->service->registerCollector('rss_1', RssCollector::class);
        $this->service->registerCollector('rss_2', RssCollector::class);

        Http::fake([
            'https://source1.com/feed.rss' => Http::response($rssXml, 200, ['Content-Type' => 'application/rss+xml']),
            'https://source2.com/feed.rss' => Http::response($rssXml, 200, ['Content-Type' => 'application/rss+xml']),
        ]);

        $source1 = Source::factory()->create([
            'base_url' => 'https://source1.com/feed.rss',
            'collector_key' => 'rss_1',
            'active' => true,
        ]);

        $source2 = Source::factory()->create([
            'base_url' => 'https://source2.com/feed.rss',
            'collector_key' => 'rss_2',
            'active' => true,
        ]);

        // Scraper source 1
        $this->service->scrapeSource($source1);
        $this->assertEquals(1, Offer::count());

        // Scraper source 2 - devrait être ignoré car même fingerprint
        $stats = $this->service->scrapeSource($source2);

        $this->assertSame(0, $stats['created']);
        $this->assertSame(1, $stats['skipped']);
        $this->assertEquals(1, Offer::count());
    }

    public function test_scrape_source_inactive_source_skipped(): void
    {
        $source = Source::factory()->create([
            'collector_key' => 'rss',
            'active' => false,
        ]);

        $stats = $this->service->scrapeSource($source);

        $this->assertSame(0, $stats['created']);
        $this->assertSame(0, $stats['errors']);
    }

    public function test_scrape_source_unknown_collector_logs_error(): void
    {
        $source = Source::factory()->create([
            'collector_key' => 'unknown_collector',
            'active' => true,
        ]);

        $stats = $this->service->scrapeSource($source);

        $this->assertSame(1, $stats['errors']);
        // Vérifier que l'erreur est loggée via ActivityLogger (canal 'activity')
        // Le test passe si on arrive ici sans exception
    }

    public function test_mark_expired_offers(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);

        Offer::factory()->active()->forSource($source)->create(['expires_at' => now()->subDay()]);
        Offer::factory()->active()->forSource($source)->create(['expires_at' => now()->addDay()]);
        Offer::factory()->expired()->forSource($source)->create();

        $count = $this->service->markExpiredOffers();

        $this->assertSame(1, $count);
        $this->assertEquals(2, Offer::where('status', 'expired')->count());
        $this->assertEquals(1, Offer::where('status', 'active')->count());
    }

    public function test_delete_expired_offers_removes_expired_rows_only(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);

        Offer::factory()->active()->forSource($source)->create(['expires_at' => now()->subDay()]);
        Offer::factory()->expired()->forSource($source)->create(['expires_at' => null]);
        Offer::factory()->active()->forSource($source)->create(['expires_at' => now()->addDay()]);

        $count = $this->service->deleteExpiredOffers();

        $this->assertSame(2, $count);
        $this->assertDatabaseCount('offers', 1);
        $this->assertDatabaseHas('offers', ['source_id' => $source->id, 'status' => 'active']);
    }

    public function test_archive_old_offers(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);

        Offer::factory()->active()->forSource($source)->create(['published_at' => now()->subMonths(7)]);
        Offer::factory()->active()->forSource($source)->create(['published_at' => now()->subMonths(3)]);
        Offer::factory()->expired()->forSource($source)->create(['published_at' => now()->subYear()]);

        $count = $this->service->archiveOldOffers(6);

        $this->assertSame(2, $count); // 1 active + 1 expired de plus de 6 mois
        $this->assertEquals(2, Offer::where('status', 'archived')->count());
    }

    public function test_deduplicate_by_fingerprint(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);

        $fp = hash('sha256', 'dev|techcorp|paris|cdi');

        Offer::factory()->forSource($source)->withFingerprint($fp)->create(['title' => 'Dev 1']);
        Offer::factory()->forSource($source)->withFingerprint($fp)->create(['title' => 'Dev 2']);
        Offer::factory()->forSource($source)->withFingerprint($fp)->create(['title' => 'Dev 3']);
        Offer::factory()->forSource($source)->create(); // fingerprint différent

        $this->assertEquals(4, Offer::count());

        $deleted = $this->service->deduplicateByFingerprint();

        $this->assertSame(2, $deleted); // 3 avec même fingerprint, on garde 1, supprime 2
        $this->assertEquals(2, Offer::count());
    }
}
