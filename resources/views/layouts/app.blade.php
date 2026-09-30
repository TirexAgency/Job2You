<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

        <title>{{ config('app.name', 'Job2You') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="app-shell d-lg-flex min-vh-100 bg-body-tertiary">
            @include('layouts.navigation')

            <div class="app-content flex-grow-1">
                <header class="app-mobile-header d-flex d-lg-none align-items-center gap-3 bg-white border-bottom px-3 py-2">
                    <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Ouvrir le menu">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2 3.5A.5.5 0 0 1 2.5 3h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0 4A.5.5 0 0 1 2.5 7h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </button>
                    <span class="fw-semibold">{{ config('app.name', 'Job2You') }}</span>
                </header>

                @isset($header)
                    <header class="bg-white border-bottom px-3 px-lg-4 py-3">
                        {{ $header }}
                    </header>
                @endisset

                <main class="app-main">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
