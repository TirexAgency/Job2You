<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Planification des tâches de scraping (CRON).
 * Exécute: php artisan schedule:run (via cron système toutes les minutes),
 * ou php artisan schedule:work en développement.
 *
 * La collecte est asynchrone : la commande dispatche ScrapeAllSourcesJob
 * sur la queue "scraping". Un worker doit donc tourner en production.
 */
Schedule::command('scraping:run --all')
    ->name('scrape-all-sources')
    ->everyTwoHours()
    ->withoutOverlapping(20)
    ->onFailure(function () {
        Log::error('Scheduler: Échec de la collecte des offres');
    });

// Nettoyage quotidien asynchrone : supprimer les offres expirées, archiver (> 6 mois), dédupliquer
Schedule::command('scraping:cleanup --delete-expired --archive=6 --deduplicate')
    ->name('scraping-cleanup')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onFailure(function () {
        Log::error('Scheduler: Échec scraping:cleanup');
    });

// Recalcul quotidien des matchings après le scraping.
Schedule::command('matches:compute')
    ->name('compute-matches')
    ->dailyAt('04:00')
    ->withoutOverlapping();
