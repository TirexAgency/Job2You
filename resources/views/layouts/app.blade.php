<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

        <title>{{ config('app.name', 'Job2You') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="app-shell d-lg-flex min-vh-100">
            @include('layouts.navigation')

            <div class="app-content flex-grow-1">
                <header class="app-mobile-header d-flex d-lg-none align-items-center gap-3 border-bottom px-3 py-2">
                    <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Ouvrir le menu">
                        <i class="bi bi-list fs-4" aria-hidden="true"></i>
                    </button>
                    <span class="fw-semibold">{{ config('app.name', 'Job2You') }}</span>
                </header>

                @isset($header)
                    <header class="border-bottom px-3 px-lg-4 py-3">
                        {{ $header }}
                    </header>
                @endisset

                <main class="app-main">
                    <div class="app-container pb-0">
                        @include('partials.flash-alerts')
                    </div>

                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
