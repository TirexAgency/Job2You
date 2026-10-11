<?php

namespace Tests\Feature\Scraping;

use App\Jobs\ScrapeSourceJob;
use App\Models\Offer;
use App\Models\Source;
use App\Services\Scraping\ScrapingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScrapeSourceJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_dispatches_and_processes(): void
    {
        Queue::fake();

        $rssXml = <<<'XML'
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

        Http::fake([
            'https://example.com/feed.rss' => Http::response($rssXml, 200, ['Content-Type' => 'application/rss+xml']),
        ]);

        $source = Source::factory()->create([
            'base_url' => 'https://example.com/feed.rss',
            'collector_key' => 'rss',
            'active' => true,
        ]);

        // Dispatcher le job
        ScrapeSourceJob::dispatch($source->id);

        // Vérifier que le job a été dispatché sur la bonne queue
        Queue::assertPushed(ScrapeSourceJob::class, function ($job) use ($source) {
            return $job->sourceId === $source->id;
        });

        // Exécuter le job en synchrone pour test
        $job = new ScrapeSourceJob($source->id);
        $job->handle(app(ScrapingService::class));

        $this->assertDatabaseHas('offers', [
            'source_id' => $source->id,
            'title' => 'Développeur Laravel',
        ]);
    }

    public function test_job_skips_inactive_source(): void
    {
        $source = Source::factory()->create([
            'collector_key' => 'rss',
            'active' => false,
        ]);

        $job = new ScrapeSourceJob($source->id);
        $job->handle(app(ScrapingService::class));

        $this->assertEquals(0, Offer::count());
    }

    public function test_job_skips_missing_source(): void
    {
        $job = new ScrapeSourceJob(99999);
        $job->handle(app(ScrapingService::class));

        $this->assertEquals(0, Offer::count());
    }

    public function test_job_has_correct_tags(): void
    {
        $job = new ScrapeSourceJob(1);

        $this->assertContains('scraping', $job->tags());
        $this->assertContains('source:1', $job->tags());
    }

    public function test_job_has_unique_id(): void
    {
        $job = new ScrapeSourceJob(42);

        $this->assertSame('scrape-source-42', $job->uniqueId());
    }
}
