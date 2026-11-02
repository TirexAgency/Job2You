<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Models\Source;
use App\Services\Scraping\ScrapingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('offers:refresh-descriptions {source? : ID ou clé du collecteur} {--force : Recharger aussi les descriptions existantes}')]
#[Description('Recharge les descriptions et métadonnées des offres depuis les pages de détail des sources')]
class RefreshOfferDescriptionsCommand extends Command
{
    public function handle(ScrapingService $scrapingService): int
    {
        $sourceArg = $this->argument('source');

        $sources = $sourceArg === null
            ? Source::all()
            : Source::query()
                ->where('collector_key', $sourceArg)
                ->orWhere('id', is_numeric($sourceArg) ? (int) $sourceArg : 0)
                ->get();

        if ($sources->isEmpty()) {
            $this->error("Aucune source trouvée pour « {$sourceArg} ».");

            return self::FAILURE;
        }

        $updated = 0;
        $processed = 0;
        $supportedSources = 0;

        foreach ($sources as $source) {
            $collectorClass = $scrapingService->getCollectors()[$source->collector_key] ?? null;

            if (! $collectorClass || ! method_exists($collectorClass, 'fetchDescription')) {
                continue;
            }

            $supportedSources++;
            $collector = app($collectorClass);
            $usesDetails = method_exists($collector, 'fetchDetails');

            $query = Offer::query()
                ->where('source_id', $source->id)
                ->whereNotNull('source_url');

            if (! $this->option('force')) {
                $query->where(function ($query) {
                    $query->whereNull('description')->orWhere('description', '')
                        ->orWhereNull('location')->orWhere('location', '')
                        ->orWhereNull('contract_type')->orWhere('contract_type', '');
                });
            }

            $total = (clone $query)->count();

            if ($total === 0) {
                $this->line("{$source->name} : aucune description ou métadonnée à recharger.");

                continue;
            }

            $this->info("{$source->name} : {$total} offre(s) à traiter.");

            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $query->chunkById(50, function ($offers) use ($collector, $usesDetails, $source, $bar, &$updated, &$processed) {
                foreach ($offers as $offer) {
                    $processed++;

                    $details = $usesDetails ? $collector->fetchDetails($offer->source_url) : null;
                    $normalized = $details ? $collector->normalize($details, $source) : [];
                    $description = $normalized['description'] ?? $collector->fetchDescription($offer->source_url);
                    $attributes = [];

                    if (filled($description)) {
                        $attributes['description'] = $description;
                    }

                    if (filled($normalized['location'] ?? null)) {
                        $attributes['location'] = $normalized['location'];
                    }

                    if (filled($normalized['contract_type'] ?? null)) {
                        $attributes['contract_type'] = $normalized['contract_type'];
                    }

                    if ($details?->expiresAt) {
                        $attributes['expires_at'] = $details->expiresAt;
                    }

                    if ($attributes !== []) {
                        $offer->update($attributes);
                        $updated++;
                    }

                    $bar->advance();
                    usleep(250000);
                }
            });

            $bar->finish();
            $this->newLine();
        }

        if ($supportedSources === 0) {
            $this->error('Aucune source compatible avec le rechargement des descriptions.');

            return self::FAILURE;
        }

        $this->info("Terminé : {$updated} offre(s) mise(s) à jour sur {$processed} offre(s) traitée(s).");

        return self::SUCCESS;
    }
}
