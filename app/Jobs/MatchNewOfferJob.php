<?php

namespace App\Jobs;

use App\Models\Offer;
use App\Services\MatchingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job pour calculer le matching d'une nouvelle offre contre tous les profils candidats.
 *
 * Dispatché automatiquement après la création d'une nouvelle offre (scraping).
 * Utilise la queue "matching" pour ne pas bloquer le scraping.
 */
class MatchNewOfferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre maximal de tentatives.
     */
    public int $tries = 3;

    /**
     * Délai entre les tentatives (secondes).
     */
    public int $backoff = 30;

    /**
     * Timeout du job (secondes).
     */
    public int $timeout = 120;

    public function __construct(
        public int $offerId,
    ) {
        $this->onQueue('matching');
    }

    /**
     * Exécute le job.
     */
    public function handle(MatchingService $matchingService): void
    {
        $offer = Offer::find($this->offerId);

        if (! $offer) {
            Log::warning('MatchNewOfferJob: Offre introuvable', ['offer_id' => $this->offerId]);

            return;
        }

        // Ne matcher que les offres actives
        if ($offer->status !== 'active') {
            Log::info('MatchNewOfferJob: Offre non active, matching ignoré', [
                'offer_id' => $offer->id,
                'status' => $offer->status,
            ]);

            return;
        }

        Log::info('MatchNewOfferJob: Début matching', [
            'offer_id' => $offer->id,
            'title' => $offer->title,
        ]);

        try {
            $count = $matchingService->computeForOffer($offer);

            Log::info('MatchNewOfferJob: Terminé', [
                'offer_id' => $offer->id,
                'matches_computed' => $count,
            ]);
        } catch (\Throwable $e) {
            Log::error('MatchNewOfferJob: Erreur', [
                'offer_id' => $offer->id,
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
        return now()->addMinutes(5);
    }

    /**
     * Tags pour le monitoring Horizon.
     */
    public function tags(): array
    {
        return [
            'matching',
            'offer:'.$this->offerId,
        ];
    }
}