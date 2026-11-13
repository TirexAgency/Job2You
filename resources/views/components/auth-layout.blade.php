@props([
    'title' => '',
    'subtitle' => null,
    'asideTitle' => 'Le poste qui vous correspond, sans y passer la journée.',
    'asideText' => 'Créez votre profil une fois. Job2You calcule le score de chaque offre et vous alerte par SMS quand elle vaut le détour.',
    'asidePoints' => [],
])

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>{{ $title }} — {{ config('app.name', 'Job2You') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-page">
        <div class="auth-shell">
            <aside class="auth-aside">
                <a href="{{ url('/') }}" class="auth-aside-brand" aria-label="Job2You, accueil">
                    <img src="{{ asset('logo.png') }}" alt="Job2You">
                </a>

                <div>
                    <h2>{{ $asideTitle }}</h2>
                    <p>{{ $asideText }}</p>

                    @if(! empty($asidePoints))
                        <ul>
                            @foreach($asidePoints as $point)
                                <li>
                                    <i class="bi bi-check-lg" aria-hidden="true"></i>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="auth-aside-foot">Job2You · Antananarivo, Madagascar</div>
            </aside>

            <div class="auth-form-side">
                <div class="auth-form-inner">
                    <a href="{{ url('/') }}" class="auth-back-home">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>Retour à l'accueil
                    </a>

                    <img class="auth-logo" src="{{ asset('logo.png') }}" alt="Job2You">

                    <h1 class="auth-title">{{ $title }}</h1>
                    @if($subtitle)
                        <p class="auth-subtitle">{{ $subtitle }}</p>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
