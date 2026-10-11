<?php

namespace App\Console\Commands;

use App\Services\Scraping\ScrapingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('scraping:cleanup {--expire : Marquer les offres expirées} {--archive= : Archiver les offres de plus de X mois} {--deduplicate : Dédupliquer par fingerprint}')]
#[Description('Nettoie les offres (expiration, archivage, déduplication)')]
class ScrapingCleanupCommand extends Command
{
    public function handle(ScrapingService $scrapingService): int
    {
        $expire = $this->option('expire');
        $archiveMonths = $this->option('archive');
        $deduplicate = $this->option('deduplicate');

        if (! $expire && $archiveMonths === null && ! $deduplicate) {
            $this->error('Aucune action spécifiée. Utilisez --expire, --archive ou --deduplicate.');

            return self::FAILURE;
        }

        $totalAffected = 0;

        if ($expire) {
            $count = $scrapingService->markExpiredOffers();
            $this->info("Offres marquées comme expirées: {$count}");
            $totalAffected += $count;
        }

        if ($archiveMonths !== null) {
            $months = (int) $archiveMonths;
            $count = $scrapingService->archiveOldOffers($months > 0 ? $months : 6);
            $this->info("Offres archivées (> {$months} mois): {$count}");
            $totalAffected += $count;
        }

        if ($deduplicate) {
            $count = $scrapingService->deduplicateByFingerprint();
            $this->info("Doublons supprimés par fingerprint: {$count}");
            $totalAffected += $count;
        }

        $this->newLine();
        $this->info("Total lignes affectées: {$totalAffected}");

        return self::SUCCESS;
    }
}
