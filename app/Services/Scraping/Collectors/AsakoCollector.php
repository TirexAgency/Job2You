<?php

namespace App\Services\Scraping\Collectors;

use App\DTO\RawOfferDTO;
use App\Models\Offer;
use App\Models\Source;
use App\Services\Scraping\SourceCollectorInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Collecteur pour Asako.mg
 * Extrait les offres depuis le JSON-LD structuré de la page /emploi
 */
class AsakoCollector implements SourceCollectorInterface
{
    public const COLLECTOR_KEY = 'rss_asako_mg';

    protected int $timeout = 30;

    protected int $maxPages = 5;

    protected int $maxDetailFetches = 60;

    protected string $baseUrl = 'https://asako.mg/emploi';

    public function collect(Source $source): array
    {
        $feedUrl = $source->base_url ?? $this->baseUrl;

        if (blank($feedUrl)) {
            Log::warning('AsakoCollector: base_url manquante', ['source_id' => $source->id]);

            return [];
        }

        try {
            $offers = [];
            $page = 1;

            while ($page <= $this->maxPages) {
                $pageUrl = $page === 1 ? $feedUrl : $feedUrl.'?page='.$page;

                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
                    ])
                    ->get($pageUrl);

                if (! $response->successful()) {
                    Log::warning('AsakoCollector: Échec récupération page', [
                        'source_id' => $source->id,
                        'url' => $pageUrl,
                        'status' => $response->status(),
                    ]);
                    break;
                }

                $pageOffers = $this->parsePage($response->body(), $source);

                if (empty($pageOffers)) {
                    break;
                }

                $offers = array_merge($offers, $pageOffers);
                $page++;

                // Pause pour ne pas surcharger le serveur
                if ($page <= $this->maxPages) {
                    usleep(500000); // 500ms
                }
            }

            return $this->enrichWithDetails($offers);
        } catch (\Throwable $e) {
            Log::error('AsakoCollector: Exception', [
                'source_id' => $source->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    /**
     * Parse la page HTML et extrait les offres depuis le JSON-LD
     *
     * @return array<RawOfferDTO>
     */
    protected function parsePage(string $html, Source $source): array
    {
        $offers = [];

        // Chercher les scripts JSON-LD de type JobPosting
        $jsonLdScripts = $this->extractJsonLd($html);

        foreach ($jsonLdScripts as $jsonLd) {
            try {
                $data = json_decode($jsonLd, true);
                if (! $data) {
                    continue;
                }

                $items = $data['@graph'] ?? [$data];

                foreach ($items as $item) {
                    if (! isset($item['@type']) || ! in_array($item['@type'], ['JobPosting', 'JobListing'])) {
                        continue;
                    }

                    $offer = $this->parseJobPosting($item);
                    if ($offer) {
                        $offers[] = $offer;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('AsakoCollector: Erreur parsing JSON-LD', [
                    'source_id' => $source->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Si pas de JSON-LD, essayer de parser le HTML directement
        if (empty($offers)) {
            $offers = $this->parseHtmlDirect($html, $source);
        }

        return $offers;
    }

    /**
     * Complète les offres sans description en lisant leur page de détail.
     * Les annonces déjà enrichies en base ne sont pas rechargées.
     *
     * @param  array<RawOfferDTO>  $offers
     * @return array<RawOfferDTO>
     */
    protected function enrichWithDetails(array $offers): array
    {
        $fetches = 0;
        $detailCache = [];

        foreach ($offers as $index => $offer) {
            $needsDetails = blank($offer->description)
                || blank($offer->location)
                || blank($offer->contractType);

            if (! $needsDetails || blank($offer->sourceUrl)) {
                continue;
            }

            if (array_key_exists($offer->sourceUrl, $detailCache)) {
                if ($detailCache[$offer->sourceUrl] !== null) {
                    $offers[$index] = $this->mergeDetail($offer, $detailCache[$offer->sourceUrl]);
                }

                continue;
            }

            if (Offer::query()
                ->where('source_url', $offer->sourceUrl)
                ->whereNotNull('description')
                ->where('description', '!=', '')
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->whereNotNull('contract_type')
                ->where('contract_type', '!=', '')
                ->exists()) {
                continue;
            }

            if ($fetches >= $this->maxDetailFetches) {
                continue;
            }

            $detail = $this->fetchDetailJobPosting($offer->sourceUrl);
            $detailCache[$offer->sourceUrl] = $detail;

            if ($detail !== null) {
                $offers[$index] = $this->mergeDetail($offer, $detail);
            }

            $fetches++;

            usleep(250000);
        }

        return $offers;
    }

    /**
     * Récupère la description d'une annonce depuis sa page de détail.
     */
    public function fetchDescription(string $url): ?string
    {
        $detail = $this->fetchDetailJobPosting($url);

        if ($detail === null) {
            return null;
        }

        return $this->parseJobPosting($detail)?->description;
    }

    /**
     * Récupère les informations complètes d'une annonce depuis sa page de détail.
     */
    public function fetchDetails(string $url): ?RawOfferDTO
    {
        $detail = $this->fetchDetailJobPosting($url);

        return $detail === null ? null : $this->parseJobPosting($detail);
    }

    /**
     * Récupère l'objet JobPosting JSON-LD d'une page de détail.
     */
    protected function fetchDetailJobPosting(string $url): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
                ])
                ->get($url);

            if (! $response->successful()) {
                Log::warning('AsakoCollector: Échec récupération détail', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $html = $response->body();

            foreach ($this->extractJsonLd($html) as $jsonLd) {
                $data = json_decode($jsonLd, true);

                if (! is_array($data)) {
                    continue;
                }

                foreach ($data['@graph'] ?? [$data] as $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $type = $item['@type'] ?? null;
                    $types = is_array($type) ? $type : [$type];

                    if (in_array('JobPosting', $types, true) || in_array('JobListing', $types, true)) {
                        if (preg_match('/type_contrat\\\\*"\s*:\s*\\\\*"([^"\\\\]+)/', $html, $contractMatch)) {
                            $item['_asako_contract_type'] = html_entity_decode(
                                stripslashes($contractMatch[1]),
                                ENT_QUOTES | ENT_HTML5,
                                'UTF-8'
                            );
                        }

                        return $item;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AsakoCollector: Exception récupération détail', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Fusionne les données de la page de détail avec l'offre de la liste.
     */
    protected function mergeDetail(RawOfferDTO $offer, array $detail): RawOfferDTO
    {
        $detailOffer = $this->parseJobPosting($detail);

        if (! $detailOffer) {
            return $offer;
        }

        return new RawOfferDTO(
            externalId: $offer->externalId,
            title: $offer->title,
            company: $offer->company ?: $detailOffer->company,
            location: $offer->location ?: $detailOffer->location,
            description: $offer->description ?: $detailOffer->description,
            contractType: $offer->contractType ?: ($detail['_asako_contract_type'] ?? $detailOffer->contractType),
            sourceUrl: $offer->sourceUrl ?: $detailOffer->sourceUrl,
            publishedAt: $offer->publishedAt ?: $detailOffer->publishedAt,
            expiresAt: $offer->expiresAt ?: $detailOffer->expiresAt,
            metadata: array_merge($offer->metadata, ['description_enriched' => true]),
        );
    }

    /**
     * Extrait les scripts JSON-LD du HTML
     */
    protected function extractJsonLd(string $html): array
    {
        $scripts = [];

        // Pattern pour <script type="application/ld+json">...</script>
        if (preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            $scripts = array_merge($scripts, $matches[1] ?? []);
        }

        return $scripts;
    }

    /**
     * Parse un objet JobPosting JSON-LD
     */
    protected function parseJobPosting(array $item): ?RawOfferDTO
    {
        $externalId = $item['identifier']['value'] ?? $item['url'] ?? $item['@id'] ?? '';
        if (blank($externalId)) {
            $externalId = Str::uuid()->toString();
        }

        $title = $item['title'] ?? $item['name'] ?? '';
        if (blank($title)) {
            return null;
        }

        // Company
        $company = null;
        if (isset($item['hiringOrganization'])) {
            $org = $item['hiringOrganization'];
            $company = is_array($org) ? ($org['name'] ?? null) : $org;
        }

        // Location
        $location = null;
        if (isset($item['jobLocation'])) {
            $loc = $item['jobLocation'];
            if (is_array($loc)) {
                $address = $loc['address'] ?? [];
                if (is_array($address)) {
                    $locality = $address['addressLocality'] ?? null;
                    $parts = array_filter([
                        $address['streetAddress'] ?? null,
                        $locality,
                        $locality ? null : ($address['addressRegion'] ?? null),
                        $this->countryName($address['addressCountry'] ?? null),
                    ]);
                    $location = implode(', ', array_unique($parts));
                }
            } elseif (is_string($loc)) {
                $location = $loc;
            }
        }

        if (blank($location) && ($item['jobLocationType'] ?? null) === 'TELECOMMUTE') {
            $requirements = $item['applicantLocationRequirements'] ?? null;
            $country = is_array($requirements)
                ? ($requirements['name'] ?? $this->applicantCountryName($requirements))
                : $this->countryName($requirements);
            $location = filled($country) ? "Télétravail ({$country})" : 'Télétravail';
        }

        // Contract type
        $contractType = $item['_asako_contract_type'] ?? null;
        if (blank($contractType) && isset($item['employmentType'])) {
            $contractType = is_array($item['employmentType'])
                ? implode(', ', $item['employmentType'])
                : $item['employmentType'];
        }

        // Description
        $description = $item['description'] ?? $item['description']['value'] ?? null;
        if ($description) {
            $description = $this->cleanHtml($description);
        }

        // Published date
        $publishedAt = null;
        if (isset($item['datePosted'])) {
            try {
                $publishedAt = CarbonImmutable::parse($item['datePosted']);
            } catch (\Throwable) {
            }
        }

        // Expires date
        $expiresAt = null;
        if (isset($item['validThrough'])) {
            try {
                $expiresAt = CarbonImmutable::parse($item['validThrough']);
            } catch (\Throwable) {
            }
        }

        // URL
        $sourceUrl = $item['url'] ?? null;

        return new RawOfferDTO(
            externalId: $externalId,
            title: trim($title),
            company: $company ? trim($company) : null,
            location: $location ? trim($location) : null,
            description: $description,
            contractType: $contractType ? trim($contractType) : null,
            sourceUrl: $sourceUrl,
            publishedAt: $publishedAt,
            expiresAt: $expiresAt,
            metadata: ['source_type' => 'json-ld'],
        );
    }

    /**
     * Parse direct du HTML si pas de JSON-LD
     *
     * @return array<RawOfferDTO>
     */
    protected function parseHtmlDirect(string $html, Source $source): array
    {
        $offers = [];

        // Asako utilise Next.js - chercher les données dans __NEXT_DATA__
        if (preg_match('/<script[^>]*id=["\']__NEXT_DATA__["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            try {
                $data = json_decode($matches[1], true);
                $offers = array_merge($offers, $this->extractFromNextData($data, $source));
            } catch (\Throwable) {
            }
        }

        // Fallback: parser les cartes d'offres dans le HTML
        if (empty($offers)) {
            $offers = array_merge($offers, $this->parseJobCards($html, $source));
        }

        return $offers;
    }

    /**
     * Extrait les offres depuis __NEXT_DATA__ (Next.js)
     */
    protected function extractFromNextData(array $data, Source $source): array
    {
        $offers = [];

        // Structure typique Next.js: props.pageProps.offers ou similaire
        $pageProps = $data['props']['pageProps'] ?? [];

        $possibleKeys = ['offers', 'jobs', 'listings', 'results', 'data', 'items'];

        foreach ($possibleKeys as $key) {
            if (isset($pageProps[$key]) && is_array($pageProps[$key])) {
                foreach ($pageProps[$key] as $item) {
                    $offer = $this->parseAsakoItem($item);
                    if ($offer) {
                        $offers[] = $offer;
                    }
                }
                break;
            }
        }

        return $offers;
    }

    /**
     * Parse un item Asako depuis leur format API
     */
    protected function parseAsakoItem(array $item): ?RawOfferDTO
    {
        // Adapter selon la structure réelle d'Asako
        $externalId = $item['id'] ?? $item['slug'] ?? $item['uuid'] ?? '';
        $title = $item['title'] ?? $item['title_fr'] ?? '';

        if (blank($title) || blank($externalId)) {
            return null;
        }

        $company = $item['company']['name'] ?? $item['company_name'] ?? $item['recruiter_name'] ?? null;
        $location = $item['location'] ?? $item['city'] ?? $item['city_name'] ?? null;
        $contractType = $item['contract_type'] ?? $item['contract'] ?? null;
        $description = $item['description'] ?? $item['description_fr'] ?? null;

        if ($description) {
            $description = $this->cleanHtml($description);
        }

        $sourceUrl = $item['url'] ?? $item['slug'] ? ('https://asako.mg/offre/'.$item['slug']) : null;

        $publishedAt = null;
        if (isset($item['published_at']) || isset($item['created_at'])) {
            try {
                $publishedAt = CarbonImmutable::parse($item['published_at'] ?? $item['created_at']);
            } catch (\Throwable) {
            }
        }

        return new RawOfferDTO(
            externalId: (string) $externalId,
            title: trim($title),
            company: $company ? trim($company) : null,
            location: $location ? trim($location) : null,
            description: $description,
            contractType: $contractType ? trim($contractType) : null,
            sourceUrl: $sourceUrl,
            publishedAt: $publishedAt,
            metadata: ['source_type' => 'asako-api'],
        );
    }

    /**
     * Parse les cartes d'offres dans le HTML brut
     */
    protected function parseJobCards(string $html, Source $source): array
    {
        $offers = [];

        // Pattern pour les cartes d'offres Asako (basé sur le HTML vu)
        // Chercher les liens vers /annonces/
        if (preg_match_all('/<a[^>]*href=["\']\/annonces\/([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $html, $matches)) {
            foreach ($matches[1] as $index => $slug) {
                // Le HTML de la carte est dans matches[2]
                $cardHtml = $matches[2][$index] ?? '';

                $offer = $this->parseAsakoCard($slug, $cardHtml);
                if ($offer) {
                    $offers[] = $offer;
                }
            }
        }

        return $offers;
    }

    protected function parseAsakoCard(string $slug, string $cardHtml): ?RawOfferDTO
    {
        $externalId = $slug;

        // Extraire le titre
        if (! preg_match('/<[^>]*class="[^"]*font-heading[^"]*"[^>]*>(.*?)<\/[^>]*>/is', $cardHtml, $titleMatch)) {
            if (! preg_match('/<h[23][^>]*>(.*?)<\/h[23]>/is', $cardHtml, $titleMatch)) {
                return null;
            }
        }
        $title = trim(strip_tags($titleMatch[1]));
        if (blank($title)) {
            return null;
        }

        // Company
        $company = null;
        if (preg_match('/<[^>]*class="[^"]*text-sm[^"]*"[^>]*style="[^"]*color:\s*#A78BCD[^"]*"[^>]*>(.*?)<\/[^>]*>/is', $cardHtml, $compMatch)) {
            $company = trim(strip_tags($compMatch[1]));
        }

        // Location
        $location = null;
        if (preg_match('/lucide lucide-map-pin[^>]*>[^<]*<\/?[^>]*>\s*([^<]+)/is', $cardHtml, $locMatch)) {
            $location = trim($locMatch[1]);
        }

        // Contract type
        $contractType = null;
        if (preg_match('/bg-asako-(?:navy|mauve|rose)-10[^>]*>\s*([A-Z]+)\s*</is', $cardHtml, $contractMatch)) {
            $contractType = trim($contractMatch[1]);
        }

        return new RawOfferDTO(
            externalId: $externalId,
            title: $title,
            company: $company,
            location: $location,
            description: null,
            contractType: $contractType,
            sourceUrl: 'https://asako.mg/annonces/'.$slug,
            publishedAt: null,
            metadata: ['source_type' => 'asako-html'],
        );
    }

    /**
     * Convertit le HTML d'une description en texte lisible en conservant
     * les paragraphes et les puces.
     */
    protected function cleanHtml(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        $html = preg_replace('#<(script|style)[^>]*>.*?</\\1>#is', '', $html);

        // Les titres deviennent des sections explicites.
        $html = preg_replace('#<h[1-6][^>]*>(.*?)</h[1-6]>#is', "\n## $1\n", $html);
        $html = preg_replace('#<p[^>]*>[\s\x{00A0}]*<(?:strong|b)[^>]*>(.*?)</(?:strong|b)>[\s\x{00A0}]*</p>#isu', "\n## $1\n", $html);

        // Les éléments de liste deviennent des puces.
        $html = preg_replace('#<li[^>]*>\s*(?:<p[^>]*>)?(.*?)(?:</p>)?\s*</li>#is', "\n- $1\n", $html);

        $html = preg_replace('#</?(br|p|div|li|ul|ol|h[1-6]|section|article|tr)[^>]*>#i', "\n", $html);
        $html = strip_tags($html);
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = str_replace("\u{00A0}", ' ', $html);
        $html = str_replace(["\r\n", "\r"], "\n", $html);
        $html = preg_replace('/[ \t]+/u', ' ', $html);
        $html = preg_replace('/ *\n */u', "\n", $html);
        $html = preg_replace('/\n{3,}/u', "\n\n", $html);
        $html = preg_replace('/\n{2,}(?=- )/u', "\n", $html);
        $html = trim($html);

        return $html !== '' ? $html : null;
    }

    public function normalize(RawOfferDTO $rawOffer, Source $source): array
    {
        $data = $rawOffer->toArray();

        $data['contract_type'] = $this->normalizeContractType($data['contract_type']);
        $data['location'] = $this->normalizeLocation($data['location']);
        $data['title'] = $this->cleanTitle($data['title']);
        $data['company'] = $data['company'] ? trim($data['company']) : null;

        if (! $data['published_at']) {
            $data['published_at'] = now();
        }

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
            'alternance' => 'Alternance',
            'apprentissage' => 'Apprentissage',
            'temps partiel' => 'Temps partiel',
            'part-time' => 'Temps partiel',
            'part_time' => 'Temps partiel',
            'part time' => 'Temps partiel',
            'temps plein' => 'Temps plein',
            'full-time' => 'Temps plein',
            'full_time' => 'Temps plein',
            'fulltime' => 'Temps plein',
            'full time' => 'Temps plein',
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

    protected function normalizeLocation(?string $location): ?string
    {
        if (blank($location)) {
            return null;
        }
        $location = trim($location);
        $parts = array_unique(array_map('trim', explode(',', $location)));

        return implode(', ', $parts);
    }

    protected function countryName(mixed $country): ?string
    {
        if (is_array($country)) {
            return $country['name'] ?? null;
        }

        if (! is_string($country) || blank($country)) {
            return null;
        }

        return match (mb_strtoupper(trim($country))) {
            'MG' => 'Madagascar',
            'FR' => 'France',
            'MU' => 'Maurice',
            'RE' => 'La Réunion',
            default => trim($country),
        };
    }

    /**
     * @param  array<mixed>  $requirements
     */
    protected function applicantCountryName(array $requirements): ?string
    {
        $names = [];

        foreach ($requirements as $requirement) {
            if (is_array($requirement) && filled($requirement['name'] ?? null)) {
                $names[] = $requirement['name'];
            } elseif (is_string($requirement)) {
                $names[] = $this->countryName($requirement);
            }
        }

        $names = array_filter(array_unique($names));

        return $names === [] ? null : implode(', ', $names);
    }

    protected function cleanTitle(string $title): string
    {
        $title = trim($title);
        $title = preg_replace('/^(offre|job|emploi|poste)\s*[:\-]\s*/i', '', $title);
        $title = preg_replace('/\s*[\(\[](H\/F|H\/F\/D|CDI|CDD|Stage|Freelance)[\)\]]\s*$/i', '', $title);

        return trim($title);
    }

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

    public static function getCollectorKey(): string
    {
        return self::COLLECTOR_KEY;
    }
}
