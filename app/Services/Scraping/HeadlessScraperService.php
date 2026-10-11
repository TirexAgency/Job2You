<?php

namespace App\Services\Scraping;

use App\Models\Offer;
use App\Models\Source;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * Service to run headless browser scrapers and sync data to the database
 */
class HeadlessScraperService
{
    protected string $scraperPath;
    protected string $nodePath;

    public function __construct()
    {
        $this->scraperPath = base_path('scrapers');
        $this->nodePath = '/usr/bin/node';
    }

    /**
     * Run the Asako headless scraper and update offers
     */
    public function scrapeAsako(): array
    {
        $scriptPath = $this->scraperPath . '/scrape_asako_json.js';
        
        if (! file_exists($scriptPath)) {
            Log::error('Headless scraper script not found', ['path' => $scriptPath]);
            return ['created' => 0, 'updated' => 0, 'errors' => 1];
        }

        try {
            $result = Process::timeout(120)
                ->path($this->scraperPath)
                ->run($this->nodePath . ' ' . $scriptPath);

            if (! $result->successful()) {
                Log::error('Asako headless scraper failed', [
                    'exit_code' => $result->exitCode(),
                    'output' => $result->output(),
                    'error' => $result->errorOutput()
                ]);
                return ['created' => 0, 'updated' => 0, 'errors' => 1];
            }

            $output = $result->output();
            
            // Parse JSON output
            $data = json_decode($output, true);
            
            if (! $data || ! isset($data['jobs'])) {
                Log::error('Invalid scraper output format', ['output' => $output]);
                return ['created' => 0, 'updated' => 0, 'errors' => 1];
            }

            return $this->syncAsakoOffers($data['jobs']);
            
        } catch (\Throwable $e) {
            Log::error('Headless scraper exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['created' => 0, 'updated' => 0, 'errors' => 1];
        }
    }

    /**
     * Sync scraped Asako jobs with database
     */
    protected function syncAsakoOffers(array $jobs): array
    {
        $source = Source::where('collector_key', 'rss_asako_mg')->first();
        
        if (! $source) {
            Log::error('Asako source not found');
            return ['created' => 0, 'updated' => 0, 'errors' => 1];
        }

        $stats = ['created' => 0, 'updated' => 0, 'errors' => 0];

        foreach ($jobs as $job) {
            try {
                $externalId = $this->extractExternalId($job['url']);
                
                if (! $externalId) {
                    continue;
                }

                // Check if offer exists
                $existing = Offer::where('source_id', $source->id)
                    ->where('external_id', $externalId)
                    ->first();

                $data = [
                    'source_id' => $source->id,
                    'external_id' => $externalId,
                    'title' => $job['title'] ?? '',
                    'company' => $job['company'] ?? null,
                    'location' => $job['location'] ?? null,
                    'contract_type' => $job['contractType'] ?? null,
                    'source_url' => $job['url'] ?? null,
                    'status' => 'active',
                ];

                if ($existing) {
                    // Update existing offer with enriched data
                    $existing->update(array_merge($existing->toArray(), $data));
                    $stats['updated']++;
                } else {
                    // Create new offer
                    Offer::create($data);
                    $stats['created']++;
                }
            } catch (\Throwable $e) {
                $stats['errors']++;
                Log::error('Error syncing Asako offer', [
                    'job' => $job,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $stats;
    }

    protected function extractExternalId(string $url): ?string
    {
        // Extract slug from Asako URL: https://asako.mg/annonces/slug
        if (preg_match('/\/annonces\/([^\/?#]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Run Tanitjobs headless scraper (for enrichment)
     */
    public function scrapeTanitjobs(): array
    {
        // Tanitjobs RSS already works well, but we could enhance with headless
        // For now, return success
        return ['created' => 0, 'updated' => 0, 'errors' => 0];
    }
}