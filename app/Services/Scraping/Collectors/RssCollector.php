<?php

namespace App\Services\Scraping\Collectors;

use App\DTO\RawOfferDTO;
use App\Models\Source;
use App\Services\Scraping\SourceCollectorInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleXMLElement;

/**
 * Collecteur générique pour flux RSS d'offres d'emploi.
 * Compatible avec les flux RSS standards (Indeed, LinkedIn, APEC, etc.)
 */
class RssCollector implements SourceCollectorInterface
{
    public const COLLECTOR_KEY = 'rss';

    protected int $timeout = 30;

    protected int $maxItems = 100;

    public function __construct(
        protected ?array $fieldMapping = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function collect(Source $source): array
    {
        $feedUrl = $source->base_url;

        if (blank($feedUrl)) {
            Log::warning('RSS Collector: base_url manquante pour la source', ['source_id' => $source->id]);

            return [];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->accept('application/rss+xml, application/xml, text/xml')
                ->get($feedUrl);

            if (! $response->successful()) {
                Log::error('RSS Collector: Échec récupération flux', [
                    'source_id' => $source->id,
                    'url' => $feedUrl,
                    'status' => $response->status(),
                ]);

                return [];
            }

            return $this->parseFeed($response->body(), $source);
        } catch (\Throwable $e) {
            Log::error('RSS Collector: Exception lors de la collecte', [
                'source_id' => $source->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    /**
     * Parse le XML RSS et extrait les offres.
     *
     * @return array<RawOfferDTO>
     */
    protected function parseFeed(string $xmlContent, Source $source): array
    {
        $offers = [];

        try {
            $xml = new SimpleXMLElement($xmlContent);
        } catch (\Throwable $e) {
            Log::error('RSS Collector: XML invalide', [
                'source_id' => $source->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }

        // Gestion des namespaces courants
        $namespaces = $xml->getNamespaces(true);
        $contentNs = $namespaces['content'] ?? null;

        $items = $xml->xpath('//item') ?: [];

        foreach (array_slice($items, 0, $this->maxItems) as $item) {
            try {
                $offer = $this->parseItem($item, $source, $contentNs);
                if ($offer) {
                    $offers[] = $offer;
                }
            } catch (\Throwable $e) {
                Log::warning('RSS Collector: Erreur parsing item', [
                    'source_id' => $source->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $offers;
    }

    /**
     * Parse un item RSS individuel.
     */
    protected function parseItem(SimpleXMLElement $item, Source $source, ?string $contentNs): ?RawOfferDTO
    {
        $mapping = $this->fieldMapping ?? $this->getDefaultMapping();

        $externalId = (string) ($item->guid ?? $item->link ?? $item->xpath('guid')[0] ?? '');
        $externalId = $externalId ?: Str::uuid()->toString();

        // Si le guid n'est pas un lien, on utilise le lien comme ID
        if (! Str::startsWith($externalId, 'http')) {
            $link = (string) ($item->link ?? '');
            if ($link) {
                $externalId = $link;
            }
        }

        $title = (string) ($item->title ?? '');
        if (blank($title)) {
            return null;
        }

        // Description : essayer content:encoded puis description
        $description = null;
        if ($contentNs && isset($item->children($contentNs)->encoded)) {
            $description = (string) $item->children($contentNs)->encoded;
        } elseif (isset($item->description)) {
            $description = (string) $item->description;
        }

        // Nettoyer le HTML de la description
        $description = $this->cleanHtml($description);

        // Extraction des champs via mapping ou défaut
        $company = $this->extractField($item, $mapping['company'] ?? ['company', 'author', 'dc:creator']);
        $location = $this->extractField($item, $mapping['location'] ?? ['location', 'city', 'geo:lat']);
        $contractType = $this->extractField($item, $mapping['contract_type'] ?? ['contract_type', 'category', 'job_type']);
        $sourceUrl = (string) ($item->link ?? '');
        $publishedAt = $this->parseDate($item, $mapping['published_at'] ?? ['pubDate', 'published', 'dc:date']);
        $expiresAt = $this->parseDate($item, $mapping['expires_at'] ?? ['expires', 'expirationDate']);

        return new RawOfferDTO(
            externalId: $externalId,
            title: $title,
            company: $company,
            location: $location,
            description: $description,
            contractType: $contractType,
            sourceUrl: $sourceUrl,
            publishedAt: $publishedAt,
            expiresAt: $expiresAt,
            metadata: [
                'categories' => $this->extractCategories($item),
                'raw_xml' => $item->asXML(),
            ],
        );
    }

    /**
     * Mapping par défaut des champs RSS.
     */
    protected function getDefaultMapping(): array
    {
        return [
            'company' => ['company', 'author', 'dc:creator', 'company_name'],
            'location' => ['location', 'city', 'region', 'geo:lat'],
            'contract_type' => ['contract_type', 'job_type', 'category', 'employment_type'],
            'published_at' => ['pubDate', 'published', 'dc:date', 'pub_date'],
            'expires_at' => ['expires', 'expirationDate', 'validThrough'],
        ];
    }

    /**
     * Extrait un champ depuis l'item en essayant plusieurs noms possibles.
     */
    protected function extractField(SimpleXMLElement $item, array $fieldNames): ?string
    {
        foreach ($fieldNames as $fieldName) {
            // Essayer attribut direct
            if (isset($item->$fieldName)) {
                $value = trim((string) $item->$fieldName);
                if ($value !== '') {
                    return $value;
                }
            }
            // Essayer avec namespace
            if (str_contains($fieldName, ':')) {
                [$ns, $local] = explode(':', $fieldName, 2);
                $namespaces = $item->getNamespaces(true);
                if (isset($namespaces[$ns]) && isset($item->children($namespaces[$ns])->$local)) {
                    $value = trim((string) $item->children($namespaces[$ns])->$local);
                    if ($value !== '') {
                        return $value;
                    }
                }
            }
            // Essayer xpath (enregistrer les namespaces si nécessaire)
            try {
                $results = $item->xpath($fieldName);
                if (! empty($results)) {
                    $value = trim((string) $results[0]);
                    if ($value !== '') {
                        return $value;
                    }
                }
            } catch (\Throwable) {
                // Ignorer les erreurs XPath (ex: namespace non déclaré)
            }
        }

        return null;
    }

    /**
     * Parse une date depuis l'item.
     */
    protected function parseDate(SimpleXMLElement $item, array $fieldNames): ?\DateTimeInterface
    {
        $dateString = $this->extractField($item, $fieldNames);
        if (! $dateString) {
            return null;
        }

        try {
            return CarbonImmutable::parse($dateString);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Extrait les catégories/tags.
     */
    protected function extractCategories(SimpleXMLElement $item): array
    {
        $categories = [];
        $results = $item->xpath('category');
        if ($results) {
            foreach ($results as $cat) {
                $val = trim((string) $cat);
                if ($val !== '') {
                    $categories[] = $val;
                }
            }
        }

        return $categories;
    }

    /**
     * Nettoie le HTML pour ne garder que le texte.
     */
    protected function cleanHtml(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        // Supprimer les balises script/style
        $html = preg_replace('#<(script|style)[^>]*>.*?</\\1>#is', '', $html);
        // Convertir <br>, <p>, <div> en sauts de ligne
        $html = preg_replace('#</?(br|p|div|li)[^>]*>#i', "\n", $html);
        // Supprimer les autres balises
        $html = strip_tags($html);
        // Nettoyer les espaces
        $html = preg_replace('/\s+/', ' ', $html);
        $html = trim($html);

        return $html !== '' ? $html : null;
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
        // Supprimer les doublons type "Paris, Paris"
        $parts = array_unique(array_map('trim', explode(',', $location)));

        return implode(', ', $parts);
    }

    /**
     * Nettoie le titre de l'offre.
     */
    protected function cleanTitle(string $title): string
    {
        $title = trim($title);
        // Supprimer préfixes courants
        $title = preg_replace('/^(offre|job|emploi|poste)\s*[:\-]\s*/i', '', $title);
        // Supprimer suffixes type "(H/F)", "(H/F/D)", "[CDI]"
        $title = preg_replace('/\s*[\(\[](H\/F|H\/F\/D|CDI|CDD|Stage|Freelance)[\)\]]\s*$/i', '', $title);

        return trim($title);
    }

    /**
     * {@inheritDoc}
     */
    public function fingerprint(array $normalizedData): string
    {
        // Fingerprint basé sur titre + compagnie + localisation + type contrat
        $components = [
            mb_strtolower(trim($normalizedData['title'] ?? '')),
            mb_strtolower(trim($normalizedData['company'] ?? '')),
            mb_strtolower(trim($normalizedData['location'] ?? '')),
            mb_strtolower(trim($normalizedData['contract_type'] ?? '')),
        ];

        $hash = hash('sha256', implode('|', $components));

        return $hash;
    }

    /**
     * {@inheritDoc}
     */
    public static function getCollectorKey(): string
    {
        return self::COLLECTOR_KEY;
    }
}
