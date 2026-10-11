<?php

namespace App\Jobs;

use App\Models\Source;
use App\Services\Scraping\ScrapingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job pour scraper toutes les sources actives.
 * Dispatch un ScrapeSourceJob par source.
 */
class ScrapeAllSourcesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $backoff = 120;

    public int $timeout = 600;

    public function __construct()
    {
        $this->onQueue('scraping');
    }

    public function handle(ScrapingService $scrapingService): void
    {
        Log::info('ScrapeAllSourcesJob: Début collecte globale');

        $sourceIds = Source::where('active', true)->pluck('id')->toArray();

        if (empty($sourceIds)) {
            Log::info('ScrapeAllSourcesJob: Aucune source active');

            return;
        }

        foreach ($sourceIds as $sourceId) {
            ScrapeSourceJob::dispatch($sourceId);
        }

        Log::info('ScrapeAllSourcesJob: Jobs dispatchés', ['count' => count($sourceIds)]);
    }

    public function tags(): array
    {
        return ['scraping', 'batch'];
    }
}
