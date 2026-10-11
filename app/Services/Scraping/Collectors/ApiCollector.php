<?php

namespace App\Services\Scraping\Collectors;

use App\DTO\RawOfferDTO;
use App\Models\Source;
use App\Services\Scraping\SourceCollectorInterface;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Collecteur générique pour API REST d'offres d'emploi.
 * Compatible avec France Travail (Pôle Emploi), Indeed API, etc.
 */
class ApiCollector implements SourceCollectorInterface
{
    public const COLLECTOR_KEY = 'api';

    protected int $timeout = 30;

    protected int $maxPages = 5;

    protected int $perPage = 50;

    public function __construct(
        protected ?array $config = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function collect(Source $source): array
    {
        $baseUrl = $source->base_url;

        if (blank($baseUrl)) {
            Log::warning('API Collector: base_url manquante pour la source', ['source_id' => $source->id]);

            return [];
        }

        $offers = [];
        $page = 1;

        while ($page <= $this->maxPages) {
            try {
                $response = $this->fetchPage($source, $page);

                if (! $response->successful()) {
                    Log::error('API Collector: Échec récupération page', [
                        'source_id' => $source->id,
                        'page' => $page,
                        'status' => $response->status(),
                    ]);
                    break;
                }

                $data = $response->json();
                $pageOffers = $this->parseResponse($data, $source);

                if (empty($pageOffers)) {
                    break;
                }

                $offers = array_merge($offers, $pageOffers);

                // Vérifier s'il y a une page suivante
                if (! $this->hasNextPage($data, $page)) {
                    break;
                }

                $page++;
            } catch (\Throwable $e) {
                Log::error('API Collector: Exception lors de la collecte', [
                    'source_id' => $source->id,
                    'page' => $page,
                    'error' => $e->getMessage(),
                ]);
                break;
            }
        }

        return $offers;
    }

    /**
     * Effectue la requête HTTP pour une page.
     */
    protected function fetchPage(Source $source, int $page): Response
    {
        $config = $this->config ?? [];
        $headers = $config['headers'] ?? [];
        $queryParams = array_merge($config['query_params'] ?? [], $this->buildPaginationParams($page));

        return Http::timeout($this->timeout)
            ->withHeaders($headers)
            ->get($source->base_url, $queryParams);
    }

    /**
     * Construit les paramètres de pagination.
     */
    protected function buildPaginationParams(int $page): array
    {
        $config = $this->config ?? [];
        $pagination = $config['pagination'] ?? [
            'page_param' => 'page',
            'per_page_param' => 'per_page',
        ];

        return [
            $pagination['page_param'] => $page,
            $pagination['per_page_param'] => $this->perPage,
        ];
    }

    /**
     * Parse la réponse JSON et extrait les offres.
     *
     * @return array<RawOfferDTO>
     */
    protected function parseResponse(array $data, Source $source): array
    {
        $offers = [];
        $itemsPath = $this->config['items_path'] ?? 'data';
        $items = data_get($data, $itemsPath, []);

        if (! is_array($items)) {
            return [];
        }

        foreach ($items as $item) {
            try {
                $offer = $this->parseItem($item);
                if ($offer) {
                    $offers[] = $offer;
                }
            } catch (\Throwable $e) {
                Log::warning('API Collector: Erreur parsing item', [
                    'source_id' => $source->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $offers;
    }

    /**
     * Parse un item individuel de la réponse API.
     */
    protected function parseItem(array $item): ?RawOfferDTO
    {
        $mapping = $this->config['field_mapping'] ?? $this->getDefaultMapping();

        $externalId = data_get($item, $mapping['external_id'] ?? 'id');
        if (blank($externalId)) {
            $externalId = Str::uuid()->toString();
        }

        $title = data_get($item, $mapping['title'] ?? 'title');
        if (blank($title)) {
            return null;
        }

        return new RawOfferDTO(
            externalId: (string) $externalId,
            title: (string) $title,
            company: data_get($item, $mapping['company'] ?? 'company'),
            location: data_get($item, $mapping['location'] ?? 'location'),
            description: data_get($item, $mapping['description'] ?? 'description'),
            contractType: data_get($item, $mapping['contract_type'] ?? 'contract_type'),
            sourceUrl: data_get($item, $mapping['source_url'] ?? 'url'),
            publishedAt: $this->parseDate(data_get($item, $mapping['published_at'] ?? 'published_at')),
            expiresAt: $this->parseDate(data_get($item, $mapping['expires_at'] ?? 'expires_at')),
            metadata: $item,
        );
    }

    /**
     * Mapping par défaut pour les APIs courantes.
     */
    protected function getDefaultMapping(): array
    {
        return [
            'external_id' => 'id',
            'title' => 'title',
            'company' => 'company.name',
            'location' => 'location.city',
            'description' => 'description',
            'contract_type' => 'contract_type',
            'source_url' => 'url',
            'published_at' => 'created_at',
            'expires_at' => 'expires_at',
        ];
    }

    /**
     * Parse une date depuis divers formats.
     */
    protected function parseDate(mixed $value): ?\DateTimeInterface
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (is_numeric($value)) {
            return CarbonImmutable::createFromTimestamp((int) $value);
        }

        try {
            return CarbonImmutable::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Vérifie s'il y a une page suivante.
     */
    protected function hasNextPage(array $data, int $currentPage): bool
    {
        $config = $this->config ?? [];
        $pagination = $config['pagination'] ?? [];

        // Vérifier via total_pages
        if (isset($pagination['total_pages_path'])) {
            $totalPages = data_get($data, $pagination['total_pages_path']);
            if ($totalPages && $currentPage < (int) $totalPages) {
                return true;
            }
        }

        // Vérifier via next_page_url ou cursor
        if (isset($pagination['next_page_path'])) {
            return ! empty(data_get($data, $pagination['next_page_path']));
        }

        // Par défaut : si on a reçu moins d'items que per_page, c'est la dernière page
        $itemsPath = $config['items_path'] ?? 'data';
        $items = data_get($data, $itemsPath, []);

        return count($items) >= $this->perPage;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(RawOfferDTO $rawOffer, Source $source): array
    {
        $data = $rawOffer->toArray();

        // Normalisation du type de contrat
        $data['contract_type'] = $this->normalizeContractType($data['contract_type']);

        // Normalisation de la localisation
        $data['location'] = $this->normalizeLocation($data['location']);

        // Nettoyage du titre
        $data['title'] = $this->cleanTitle($data['title']);

        // Nettoyage de la compagnie
        $data['company'] = $data['company'] ? trim($data['company']) : null;

        // Assurer que published_at est défini
        if (! $data['published_at']) {
            $data['published_at'] = now();
        }

        // Générer le fingerprint
        $data['fingerprint'] = $this->fingerprint($data);

        return [
            'source_id' => $source->id,
            'external_id' => $data['external_id'],
            'title' => $data['title'],
            'company' => $data['company'],
            'location' => $data['location'],
            'description' => $data['description'],
            'contract_type' => $data['contract_type'],
            'source_url' => $data['source_url'],
            'published_at' => $data['published_at'],
            'expires_at' => $data['expires_at'],
            'fingerprint' => $data['fingerprint'],
            'status' => 'active',
        ];
    }

    /**
     * Normalise le type de contrat.
     */
    protected function normalizeContractType(?string $type): ?string
    {
        if (blank($type)) {
            return null;
        }

        $type = mb_strtolower(trim($type));
        $mapping = [
            'cdi' => 'CDI',
            'cdd' => 'CDD',
            'freelance' => 'Freelance',
            'stage' => 'Stage',
            'internship' => 'Stage',
            'apprentissage' => 'Apprentissage',
            'alternance' => 'Alternance',
            'temps partiel' => 'Temps partiel',
            'part-time' => 'Temps partiel',
            'temps plein' => 'Temps plein',
            'full-time' => 'Temps plein',
            'contract' => 'CDD',
            'permanent' => 'CDI',
            'temporary' => 'CDD',
        ];

        foreach ($mapping as $key => $value) {
            if (str_contains($type, $key)) {
                return $value;
            }
        }

        return ucfirst($type);
    }

    /**
     * Normalise la localisation.
     */
    protected function normalizeLocation(?string $location): ?string
    {
        if (blank($location)) {
            return null;
        }

        $location = trim($location);
        $parts = array_unique(array_map('trim', explode(',', $location)));

        return implode(', ', $parts);
    }

    /**
     * Nettoie le titre de l'offre.
     */
    protected function cleanTitle(string $title): string
    {
        $title = trim($title);
        $title = preg_replace('/^(offre|job|emploi|poste)\s*[:\-]\s*/i', '', $title);
        $title = preg_replace('/\s*[\(\[](H\/F|H\/F\/D|CDI|CDD|Stage|Freelance)[\)\]]\s*$/i', '', $title);

        return trim($title);
    }

    /**
     * {@inheritDoc}
     */
    public function fingerprint(array $normalizedData): string
    {
        $components = [
            mb_strtolower(trim($normalizedData['title'] ?? '')),
            mb_strtolower(trim($normalizedData['company'] ?? '')),
            mb_strtolower(trim($normalizedData['location'] ?? '')),
            mb_strtolower(trim($normalizedData['contract_type'] ?? '')),
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * {@inheritDoc}
     */
    public static function getCollectorKey(): string
    {
        return self::COLLECTOR_KEY;
    }
}
