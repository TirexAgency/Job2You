@extends('layouts.app')

@section('title', 'Accueil - Job2You')

@section('content')
<div class="container">
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Bienvenue sur Job2You</h1>
            <p class="col-md-8 fs-4">
                Plateforme de matching d'offres d'emploi avec alertes SMS automatisées.
                Trouvez le job qui vous correspond, recevez les offres compatibles directement sur votre téléphone.
            </p>
            <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-lg">Voir les offres</a>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg">Créer un compte</a>
        </div>
    </div>
</div>
