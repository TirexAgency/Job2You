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
 * Job pour scraper une source d'offres d'emploi.
 *
 * Utilise le queue worker pour traiter en arrière-plan.
 * Implémente ShouldQueue pour être dispatché sur la queue.
 */
class ScrapeSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre maximal de tentatives.
     */
    public int $tries = 3;

    /**
     * Délai entre les tentatives (secondes).
     */
    public int $backoff = 60;

    /**
     * Timeout du job (secondes).
     */
    public int $timeout = 300;

    public function __construct(
        public int $sourceId,
    ) {
        $this->onQueue('scraping');
    }

    /**
     * Exécute le job.
     */
    public function handle(ScrapingService $scrapingService): void
    {
        $source = Source::find($this->sourceId);

        if (! $source) {
            Log::warning('ScrapeSourceJob: Source introuvable', ['source_id' => $this->sourceId]);

            return;
        }

        if (! $source->active) {
            Log::info('ScrapeSourceJob: Source inactive, ignorée', ['source_id' => $source->id]);

            return;
        }

        Log::info('ScrapeSourceJob: Début', [
            'source_id' => $source->id,
            'source_name' => $source->name,
            'collector_key' => $source->collector_key,
        ]);

        try {
            $stats = $scrapingService->scrapeSource($source);

            Log::info('ScrapeSourceJob: Terminé', [
                'source_id' => $source->id,
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            Log::error('ScrapeSourceJob: Erreur', [
                'source_id' => $source->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Détermine si le job doit être relancé en cas d'échec.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }

    /**
     * Tags pour le monitoring Horizon.
     */
    public function tags(): array
    {
        return [
            'scraping',
            'source:'.$this->sourceId,
        ];
    }
}
