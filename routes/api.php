<?php

use App\Jobs\ScrapeAllSourcesJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// Cron externe - déclenche le scraping de toutes les sources actives
// Protégé par token secret dans header X-Cron-Token
// Utilise le middleware "api" (pas de session, pas de CSRF)
Route::post('/cron/scrape-all', function (Request $request) {
    $expectedToken = config('app.cron_token');
    
    if (! $expectedToken) {
        Log::error('Cron scrape-all: CRON_TOKEN non configuré');
        return response()->json(['error' => 'Configuration manquante'], 500);
    }
    
    $providedToken = $request->header('X-Cron-Token');
    
    if (! hash_equals($expectedToken, (string) $providedToken)) {
        Log::warning('Cron scrape-all: Token invalide', ['ip' => $request->ip()]);
        return response()->json(['error' => 'Token invalide'], 403);
    }
    
    ScrapeAllSourcesJob::dispatch();
    
    Log::info('Cron scrape-all: Job dispatché', ['ip' => $request->ip()]);
    
    return response()->json(['status' => 'dispatched']);
})->name('cron.scrape-all');