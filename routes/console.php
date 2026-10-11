<?php

use App\Jobs\ScrapeAllSourcesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Planification des tâches de scraping (CRON).
 * Exécute: php artisan schedule:run (via cron système toutes les minutes)
 */
Schedule::job(new ScrapeAllSourcesJob)
    ->name('scrape-all-sources')
    ->hourly()
    ->withoutOverlapping(10 * 60) // Éviter le chevauchement (10 min max)
    ->onFailure(function () {
        Log::error('Scheduler: Échec ScrapeAllSourcesJob');
    });

// Nettoyage quotidien : expirer, archiver (> 6 mois), dédupliquer
Schedule::command('scraping:cleanup --expire --archive=6 --deduplicate')
    ->name('scraping-cleanup')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onFailure(function () {
        Log::error('Scheduler: Échec scraping:cleanup');
    });

// Optionnel: Recalcul des matchings après scraping (si MatchingService existe)
/*
Schedule::command('matches:compute')
    ->name('compute-matches')
    ->dailyAt('04:00')
    ->withoutOverlapping();
*/
