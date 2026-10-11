<?php

namespace Tests\Feature\Scraping;

use App\Models\Offer;
use App\Models\Source;
use App\Services\Scraping\ScrapingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScrapingCommandsTest extends TestCase
{
    use RefreshDatabase;

    protected ScrapingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ScrapingService::class);

        $this->rssXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Job Feed</title>
        <item>
            <title>Développeur Laravel</title>
            <link>https://example.com/job/1</link>
            <guid>https://example.com/job/1</guid>
            <description>Description</description>
            <pubDate>Mon, 01 Jan 2026 10:00:00 +0000</pubDate>
            <author>TechCorp</author>
        </item>
    </channel>
</rss>
XML;
    }

    public function test_scraping_run_command_single_source_sync(): void
    {
        Http::fake([
            'https://example.com/feed.rss' => Http::response($this->rssXml, 200, ['Content-Type' => 'application/rss+xml']),
        ]);

        $source = Source::factory()->create([
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss',
            'active' => true,
        ]);

        $this->artisan('scraping:run', ['source' => (string) $source->id, '--sync' => true])
            ->expectsOutputToContain('Collecte pour la source')
            ->expectsOutputToContain('Créées')
            ->assertExitCode(0);

        $this->assertDatabaseHas('offers', [
            'source_id' => $source->id,
            'title' => 'Développeur Laravel',
        ]);
    }

    public function test_scraping_run_command_by_collector_key(): void
    {
        Http::fake([
            'https://example.com/feed.rss' => Http::response($this->rssXml, 200, ['Content-Type' => 'application/rss+xml']),
        ]);

        $source = Source::factory()->create([
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss_test',
            'active' => true,
        ]);

        $this->artisan('scraping:run', ['source' => 'rss_test', '--sync' => true])
            ->expectsOutputToContain('Collecte pour la source')
            ->assertExitCode(0);
    }

    public function test_scraping_cleanup_command_expire(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);

        Offer::factory()->active()->forSource($source)->create(['expires_at' => now()->subDay()]);
        Offer::factory()->active()->forSource($source)->create(['expires_at' => now()->addDay()]);

        $this->artisan('scraping:cleanup', ['--expire' => true])
            ->expectsOutputToContain('Offres marquées comme expirées: 1')
            ->assertExitCode(0);

        $this->assertEquals(1, Offer::where('status', 'expired')->count());
    }

    public function test_scraping_cleanup_command_archive(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);

        Offer::factory()->active()->forSource($source)->create(['published_at' => now()->subMonths(7)]);
        Offer::factory()->active()->forSource($source)->create(['published_at' => now()->subMonths(3)]);

        $this->artisan('scraping:cleanup', ['--archive' => 6])
            ->expectsOutputToContain('Offres archivées')
            ->assertExitCode(0);

        $this->assertEquals(1, Offer::where('status', 'archived')->count());
    }

    public function test_scraping_cleanup_command_deduplicate(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);

        $fp = hash('sha256', 'dev|techcorp|paris|cdi');

        Offer::factory()->forSource($source)->withFingerprint($fp)->create();
        Offer::factory()->forSource($source)->withFingerprint($fp)->create();
        Offer::factory()->forSource($source)->withFingerprint($fp)->create();
        Offer::factory()->forSource($source)->create(); // different fingerprint

        $this->artisan('scraping:cleanup', ['--deduplicate' => true])
            ->expectsOutputToContain('Doublons supprimés par fingerprint: 2')
            ->assertExitCode(0);

        $this->assertEquals(2, Offer::count());
    }

    public function test_scraping_cleanup_command_requires_action(): void
    {
        $this->artisan('scraping:cleanup')
            ->expectsOutputToContain('Aucune action spécifiée')
            ->assertExitCode(1);
    }
}
