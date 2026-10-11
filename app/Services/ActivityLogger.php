<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Logger d'activité métier - messages lisibles par des non-développeurs.
 * Écrit dans le canal 'activity' (fichier storage/logs/activity.log)
 */
class ActivityLogger
{
    /**
     * Récupère le logger d'activité de façon sécurisée.
     */
    private function getLogger(): ?LoggerInterface
    {
        try {
            return Log::channel('activity')->getLogger();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Log le début d'une collecte pour une source.
     */
    public function scrapingStarted(string $sourceName, string $collectorType): void
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->info('🔄 Collecte démarrée', [
                'action' => 'scraping_started',
                'source' => $sourceName,
                'type_collecteur' => $this->collectorLabel($collectorType),
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }

    /**
     * Log la fin d'une collecte avec résultats.
     */
    public function scrapingCompleted(
        string $sourceName,
        int $created,
        int $updated,
        int $skipped,
        int $errors
    ): void {
        $message = "✅ Collecte terminée : {$created} nouvelle(s), {$updated} mise(s) à jour, {$skipped} doublon(s), {$errors} erreur(s)";
        if ($errors > 0) {
            $message = "⚠️ Collecte terminée avec erreurs : {$created} nouvelle(s), {$updated} mise(s) à jour, {$skipped} doublon(s), {$errors} erreur(s)";
        } elseif ($created === 0 && $updated === 0 && $skipped === 0) {
            $message = 'ℹ️ Collecte terminée : aucune nouvelle offre trouvée';
        }

        $logger = $this->getLogger();
        if ($logger) {
            $logger->info($message, [
                'action' => 'scraping_completed',
                'source' => $sourceName,
                'nouvelles' => $created,
                'mises_a_jour' => $updated,
                'doublons' => $skipped,
                'erreurs' => $errors,
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }

    /**
     * Log qu'aucune offre n'a été trouvée.
     */
    public function scrapingEmpty(string $sourceName): void
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->info('📭 Aucune offre trouvée pour cette source', [
                'action' => 'scraping_empty',
                'source' => $sourceName,
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }

    /**
     * Log une erreur de collecte.
     */
    public function scrapingFailed(string $sourceName, string $reason): void
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->error("❌ Échec de la collecte : {$reason}", [
                'action' => 'scraping_failed',
                'source' => $sourceName,
                'raison' => $reason,
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }

    /**
     * Log qu'un collecteur n'est pas configuré.
     */
    public function collectorMissing(string $sourceName, string $collectorKey): void
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->warning("⚠️ Collecteur non configuré : '{$collectorKey}' pour la source « {$sourceName} »", [
                'action' => 'collector_missing',
                'source' => $sourceName,
                'cle_collecteur' => $collectorKey,
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }

    /**
     * Log le nettoyage des offres expirées.
     */
    public function cleanupExpired(int $count): void
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->info("🗑️ Nettoyage : {$count} offre(s) marquée(s) comme expirée(s)", [
                'action' => 'cleanup_expired',
                'nombre' => $count,
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }

    /**
     * Log l'archivage des vieilles offres.
     */
    public function cleanupArchived(int $count, int $months): void
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->info("📦 Archivage : {$count} offre(s) de plus de {$months} mois archivée(s)", [
                'action' => 'cleanup_archived',
                'nombre' => $count,
                'mois' => $months,
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }

    /**
     * Log la déduplication.
     */
    public function cleanupDeduplicated(int $count): void
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->info("🔍 Déduplication : {$count} doublon(s) supprimé(s)", [
                'action' => 'cleanup_deduplicated',
                'nombre' => $count,
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }

    /**
     * Label lisible pour le type de collecteur.
     */
    private function collectorLabel(string $key): string
    {
        return match ($key) {
            'rss' => 'Flux RSS',
            'api' => 'API REST',
            default => $key,
        };
    }

    /**
     * Log générique d'action admin.
     */
    public function adminAction(string $action, string $details = ''): void
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->info("👤 Action admin : {$action}".($details ? " — {$details}" : ''), [
                'action' => 'admin_action',
                'details' => $details,
                'horodatage' => now()->format('d/m/Y H:i:s'),
            ]);
        }
    }
}
