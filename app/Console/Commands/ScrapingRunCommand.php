<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeAllSourcesJob;
use App\Jobs\ScrapeSourceJob;
use App\Models\Source;
use App\Services\Scraping\ScrapingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('scraping:run {source? : Clé du collecteur ou ID de la source} {--sync : Exécuter en synchrone (sans queue)} {--all : Scraper toutes les sources actives}')]
#[Description('Lance la collecte d\'offres pour une source ou toutes les sources')]
class ScrapingRunCommand extends Command
{
    public function handle(ScrapingService $scrapingService): int
    {
        $sourceArg = $this->argument('source');
        $sync = $this->option('sync');
        $all = $this->option('all');

        if ($all || (! $sourceArg && ! $sync)) {
            return $this->runAll($sync, $scrapingService);
        }

        return $this->runSingle($sourceArg, $sync, $scrapingService);
    }

    protected function runAll(bool $sync, ScrapingService $scrapingService): int
    {
        $this->info('Lancement de la collecte pour toutes les sources actives...');

        if ($sync) {
            $results = $scrapingService->scrapeAllActive();

            $totalCreated = 0;
            $totalUpdated = 0;
            $totalSkipped = 0;
            $totalErrors = 0;

            foreach ($results as $key => $stats) {
                $this->line("  <comment>{$key}</comment>: {$stats['created']} créées, {$stats['updated']} MAJ, {$stats['skipped']} ignorées, {$stats['errors']} erreurs");
                $totalCreated += $stats['created'];
                $totalUpdated += $stats['updated'];
                $totalSkipped += $stats['skipped'];
                $totalErrors += $stats['errors'];
            }

            $this->newLine();
            $this->info("Total: {$totalCreated} créées, {$totalUpdated} MAJ, {$totalSkipped} ignorées, {$totalErrors} erreurs");

            return self::SUCCESS;
        }

        ScrapeAllSourcesJob::dispatch();

        $this->info('Job de collecte globale dispatché sur la queue "scraping".');

        return self::SUCCESS;
    }

    protected function runSingle(string $sourceArg, bool $sync, ScrapingService $scrapingService): int
    {
        $source = $this->resolveSource($sourceArg);

        if (! $source) {
            $this->error("Source introuvable: {$sourceArg}");

            return self::FAILURE;
        }

        if (! $source->active) {
            $this->warn("Source '{$source->name}' est inactive.");

            return self::FAILURE;
        }

        $this->info("Collecte pour la source: {$source->name} ({$source->collector_key})");

        if ($sync) {
            $stats = $scrapingService->scrapeSource($source);

            $this->newLine();
            $this->table(
                ['Métrique', 'Valeur'],
                [
                    ['Créées', $stats['created']],
                    ['Mis à jour', $stats['updated']],
                    ['Ignorées (doublons)', $stats['skipped']],
                    ['Erreurs', $stats['errors']],
                ]
            );

            return self::SUCCESS;
        }

        ScrapeSourceJob::dispatch($source->id);

        $this->info("Job dispatché sur la queue 'scraping' pour la source #{$source->id}.");

        return self::SUCCESS;
    }

    protected function resolveSource(string $arg): ?Source
    {
        // Essayer par ID
        if (is_numeric($arg)) {
            return Source::find((int) $arg);
        }

        // Essayer par collector_key
        return Source::where('collector_key', $arg)->first();
    }
}
