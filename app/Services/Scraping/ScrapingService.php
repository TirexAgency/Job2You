<?php

namespace App\Services\Scraping;

use App\DTO\RawOfferDTO;
use App\Models\Offer;
use App\Models\Source;
use App\Services\ActivityLogger;
use App\Services\Scraping\Collectors\AsakoCollector;
use App\Services\Scraping\Collectors\RssCollector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service principal d'orchestration du scraping.
 * Gère la collecte, normalisation, déduplication et persistance des offres.
 */
class ScrapingService
{
    /**
     * Registre des collecteurs disponibles.
     *
     * @var array<string, class-string<SourceCollectorInterface>>
     */
    protected array $collectors = [
        RssCollector::COLLECTOR_KEY => RssCollector::class,
        // Collecteurs RSS Madagascar
        'rss_emploi_mg' => RssCollector::class,
        'rss_madacareers' => RssCollector::class,
        'rss_jobmada' => RssCollector::class,
        'rss_tanitjobs' => RssCollector::class,
        'rss_miarakajobs' => RssCollector::class,
        'rss_fb_mg_it' => RssCollector::class,
        // Nouveaux sites Madagascar (asako, portaljob) - collecteur HTML/JSON-LD
        'rss_asako_mg' => AsakoCollector::class,
        'rss_portaljob_mg' => AsakoCollector::class,
    ];

    /**
     * Configuration par collecteur.
     *
     * @var array<string, array>
     */
    protected array $collectorConfigs = [
        'rss' => [],
        'api' => [],
    ];

    public function __construct(
        protected ActivityLogger $activityLogger,
    ) {}

    /**
     * Exécute la collecte pour une source donnée.
     *
     * @return array{created: int, updated: int, skipped: int, errors: int}
     */
    public function scrapeSource(Source $source): array
    {
        $collector = $this->resolveCollector($source);

        if (! $collector) {
            $this->activityLogger->collectorMissing($source->name, $source->collector_key);

            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 1];
        }

        $this->activityLogger->scrapingStarted($source->name, $source->collector_key);

        $rawOffers = $collector->collect($source);

        if (empty($rawOffers)) {
            $this->activityLogger->scrapingEmpty($source->name);
            $source->update(['last_run_at' => now()]);

            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];
        }

        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($rawOffers as $rawOffer) {
            try {
                $result = $this->persistOffer($rawOffer, $source, $collector);
                $stats[$result]++;
            } catch (\Throwable $e) {
                $stats['errors']++;
                Log::error('ScrapingService: Erreur persistance offre', [
                    'source_id' => $source->id,
                    'external_id' => $rawOffer->externalId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $source->update(['last_run_at' => now()]);

        $this->activityLogger->scrapingCompleted(
            $source->name,
            $stats['created'],
            $stats['updated'],
            $stats['skipped'],
            $stats['errors']
        );

        return $stats;
    }

    /**
     * Exécute la collecte pour toutes les sources actives.
     *
     * @return array<string, array{created: int, updated: int, skipped: int, errors: int}>
     */
    public function scrapeAllActive(): array
    {
        $results = [];

        Source::where('active', true)->each(function (Source $source) use (&$results) {
            $results[$source->collector_key] = $this->scrapeSource($source);
        });

        return $results;
    }

    /**
     * Persiste une offre en base avec déduplication.
     */
    protected function persistOffer(RawOfferDTO $rawOffer, Source $source, SourceCollectorInterface $collector): string
    {
        $normalizedData = $collector->normalize($rawOffer, $source);
        $fingerprint = $normalizedData['fingerprint'];

        return DB::transaction(function () use ($normalizedData, $fingerprint, $source) {
            // Vérifier si une offre avec le même fingerprint existe déjà (toutes sources)
            $existingByFingerprint = Offer::where('fingerprint', $fingerprint)
                ->where('status', '!=', 'archived')
                ->first();

            if ($existingByFingerprint) {
                // Mettre à jour last_seen_at si on veut tracker
                return 'skipped';
            }

            // Vérifier si l'offre existe déjà pour cette source (clé unique source_id + external_id)
            $existing = Offer::where('source_id', $source->id)
                ->where('external_id', $normalizedData['external_id'])
                ->first();

            if ($existing) {
                // Mettre à jour les champs modifiables
                $existing->update([
                    'title' => $normalizedData['title'],
                    'company' => $normalizedData['company'],
                    'location' => $normalizedData['location'],
                    'description' => $normalizedData['description'],
                    'contract_type' => $normalizedData['contract_type'],
                    'source_url' => $normalizedData['source_url'],
                    'published_at' => $normalizedData['published_at'],
                    'expires_at' => $normalizedData['expires_at'],
                    'fingerprint' => $fingerprint,
                    'status' => 'active',
                ]);

                return 'updated';
            }

            // Créer nouvelle offre
            Offer::create($normalizedData);

            return 'created';
        });
    }

    /**
     * Résout le collecteur pour une source.
     */
    protected function resolveCollector(Source $source): ?SourceCollectorInterface
    {
        $key = $source->collector_key;

        if (! isset($this->collectors[$key])) {
            return null;
        }

        $collectorClass = $this->collectors[$key];
        $config = $this->collectorConfigs[$key] ?? [];

        return app($collectorClass, ['config' => $config]);
    }

    /**
     * Enregistre un nouveau collecteur.
     */
    public function registerCollector(string $key, string $collectorClass, array $config = []): void
    {
        $this->collectors[$key] = $collectorClass;
        $this->collectorConfigs[$key] = $config;
    }

    /**
     * Retourne les collecteurs enregistrés.
     *
     * @return array<string, class-string<SourceCollectorInterface>>
     */
    public function getCollectors(): array
    {
        return $this->collectors;
    }

    /**
     * Marque les offres expirées.
     */
    public function markExpiredOffers(): int
    {
        return Offer::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    /**
     * Archive les offres anciennes (ex: > 6 mois).
     */
    public function archiveOldOffers(int $months = 6): int
    {
        $cutoff = now()->subMonths($months);

        return Offer::where('status', '!=', 'archived')
            ->where('published_at', '<', $cutoff)
            ->update(['status' => 'archived']);
    }

    /**
     * Nettoie les doublons par fingerprint (garde le plus récent).
     */
    public function deduplicateByFingerprint(): int
    {
        $deleted = 0;

        $duplicates = DB::table('offers')
            ->select('fingerprint', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('fingerprint')
            ->groupBy('fingerprint')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            $deleted += Offer::where('fingerprint', $dup->fingerprint)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        return $deleted;
    }
}
