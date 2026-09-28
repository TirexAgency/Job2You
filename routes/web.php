<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Routes publiques et authentifiées du MVP Job2You.
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// Routes d'authentification (à générer avec laravel/ui ou manuellement)
// Auth::routes();

// Routes des offres (à développer à l'étape 3)
Route::get('/jobs', function () {
    return view('home'); // Temporaire
})->name('jobs.index');

// Routes du profil candidat (à développer à l'étape 3)
Route::get('/profile', function () {
    return view('home'); // Temporaire
})->middleware('auth')->name('profile');
