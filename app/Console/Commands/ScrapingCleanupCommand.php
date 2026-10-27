<?php

namespace App\Console\Commands;

use App\Services\ActivityLogger;
use App\Services\Scraping\ScrapingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('scraping:cleanup {--expire : Marquer les offres expirées} {--delete-expired : Supprimer définitivement les offres expirées} {--archive= : Archiver les offres de plus de X mois} {--deduplicate : Dédupliquer par fingerprint}')]
#[Description('Nettoie les offres (expiration, suppression, archivage, déduplication)')]
class ScrapingCleanupCommand extends Command
{
    public function handle(ScrapingService $scrapingService, ActivityLogger $activityLogger): int
    {
        $expire = $this->option('expire');
        $deleteExpired = $this->option('delete-expired');
        $archiveMonths = $this->option('archive');
        $deduplicate = $this->option('deduplicate');

        if (! $expire && ! $deleteExpired && $archiveMonths === null && ! $deduplicate) {
            $this->error('Aucune action spécifiée. Utilisez --expire, --delete-expired, --archive ou --deduplicate.');

            return self::FAILURE;
        }

        $totalAffected = 0;

        if ($expire) {
            $count = $scrapingService->markExpiredOffers();
            $this->info("Offres marquées comme expirées: {$count}");
            $activityLogger->cleanupExpired($count);
            $totalAffected += $count;
        }

        if ($deleteExpired) {
            $count = $scrapingService->deleteExpiredOffers();
            $this->info("Offres expirées supprimées: {$count}");
            $activityLogger->cleanupDeleted($count);
            $totalAffected += $count;
        }

        if ($archiveMonths !== null) {
            $months = (int) $archiveMonths;
            $count = $scrapingService->archiveOldOffers($months > 0 ? $months : 6);
            $this->info("Offres archivées (> {$months} mois): {$count}");
            $activityLogger->cleanupArchived($count, $months > 0 ? $months : 6);
            $totalAffected += $count;
        }

        if ($deduplicate) {
            $count = $scrapingService->deduplicateByFingerprint();
            $this->info("Doublons supprimés par fingerprint: {$count}");
            $activityLogger->cleanupDeduplicated($count);
            $totalAffected += $count;
        }

        $this->newLine();
        $this->info("Total lignes affectées: {$totalAffected}");

        return self::SUCCESS;
    }
}
