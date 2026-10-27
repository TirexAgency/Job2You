<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>Offres d'emploi — {{ config('app.name', 'Job2You') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #004ac6;
            --primary-container: #2563eb;
            --on-primary: #ffffff;
            --on-surface: #111c2d;
            --on-surface-variant: #434655;
            --surface: #f9f9ff;
            --surface-container-low: #f0f3ff;
            --surface-container: #e7eeff;
            --surface-container-high: #dee8ff;
            --surface-container-lowest: #ffffff;
            --tertiary: #006329;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--surface);
            color: var(--on-surface);
        }

        .navbar {
            background: rgba(249, 249, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 8px rgba(0,0,0,0.04);
        }

        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: var(--surface-container-lowest);
                padding: 1rem;
                border-radius: 0.5rem;
                margin-top: 0.5rem;
            }
        }

        .navbar-brand img {
            height: 2.5rem;
            width: 10rem;
            object-fit: cover;
            object-position: center 46%;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--on-surface-variant) !important;
            transition: color 0.2s;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary) !important;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary) !important;
            color: var(--on-primary);
        }

        .btn-primary:hover {
            background: var(--primary-container);
            border-color: var(--primary-container);
            color: var(--on-primary);
        }

        .hero {
            background: rgba(240, 243, 255, 0.7);
            padding-top: 8rem;
            padding-bottom: 4rem;
        }

        .search-bar {
            background: var(--surface-container-lowest);
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 1rem;
        }

        .search-input-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: var(--surface-container-low);
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .search-input-group:focus-within {
            background: var(--surface-container-lowest);
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        }

        .search-input-group i {
            font-size: 1.25rem;
            color: #737686;
        }

        .search-input-group input,
        .search-input-group select {
            background: transparent;
            border: none;
            outline: none;
            padding: 0;
            width: 100%;
            font-size: 0.875rem;
            color: var(--on-surface);
        }

        .search-input-group input::placeholder {
            color: #737686;
        }

        .offer-card {
            background: var(--surface-container-lowest);
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            transition: all 0.2s;
            height: 100%;
        }

        .offer-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .offer-logo {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            object-fit: cover;
        }

        .offer-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: var(--surface-container);
            color: var(--on-surface);
        }

        .skill-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e7f1ff;
            color: var(--primary) !important;
        }

        .offer-score {
            font-size: 0.75rem;
            color: var(--tertiary);
            font-weight: 600;
        }

        .offer-link {
            color: var(--primary) !important;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .offer-link:hover {
            text-decoration: underline;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #dbe1ff;
            color: #003ea8;
        }

        .badge .dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
            background: var(--primary);
            margin-right: 0.25rem;
            display: inline-block;
        }

        .footer {
            background: var(--surface-container-low);
            box-shadow: 0 -1px 8px rgba(0,0,0,0.02);
        }

        .footer a {
            color: var(--on-surface-variant) !important;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: var(--primary) !important;
        }

        .pagination {
            --bs-pagination-color: var(--primary) !important;
            --bs-pagination-active-bg: var(--primary);
            --bs-pagination-active-border-color: var(--primary) !important;
        }

        /* États de chargement AJAX */
        .offers-loading .offer-card {
            opacity: 0.5;
            pointer-events: none;
        }

        .spinner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 0.75rem;
        }

        /* Filtres avancés */
        .filter-toggle {
            cursor: pointer;
        }

        .filter-panel {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <!-- Hero -->
        <section class="public-hero">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8">
                        <span class="public-eyebrow">
                            <i class="bi bi-briefcase-fill" aria-hidden="true"></i>
                            Le marché tech, en un seul endroit
                        </span>
                        <h1 class="public-hero-title">Des offres qui vous <span class="text-gradient">correspondent</span>.</h1>
                        <p class="public-hero-lead mx-auto">Filtrez par métier, ville ou contrat et trouvez l'opportunité qui fera avancer votre carrière.</p>
                    </div>
                </div>

                <div class="row justify-content-center mt-2">
                    <div class="col-xl-10">
                        <div class="public-search-card">
                            <form id="search-form" action="{{ route('offers.index') }}" method="GET">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <div class="public-search-field">
                                            <i class="bi bi-briefcase" aria-hidden="true"></i>
                                            <input type="text" name="search" id="search-input" class="form-control" placeholder="Métier, compétence..." value="{{ request('search') }}" aria-label="Métier ou compétence">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="public-search-field">
                                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                            <input type="text" name="location" id="location-input" class="form-control" placeholder="Localisation..." value="{{ request('location') }}" aria-label="Localisation">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="public-search-field">
                                            <i class="bi bi-file-text" aria-hidden="true"></i>
                                            <select name="contract_type" id="contract-type-input" class="form-select" aria-label="Type de contrat">
                                                <option value="">Type de contrat</option>
                                                @foreach($contractTypes as $type)
                                                    <option value="{{ $type }}" {{ request('contract_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary public-search-submit w-100" id="search-submit">
                                            <i class="bi bi-search me-1" aria-hidden="true"></i>Rechercher
                                        </button>
                                    </div>
                                </div>

                                <!-- Filtres avancés -->
                                <div class="row g-2 align-items-center mt-3 filter-panel" id="advanced-filters" style="display: none;">
                                    <div class="col-md-3">
                                        <div class="public-search-field">
                                            <i class="bi bi-calendar" aria-hidden="true"></i>
                                            <input type="date" name="date_from" id="date-from-input" class="form-control" value="{{ request('date_from') }}" aria-label="Date de début">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="public-search-field">
                                            <i class="bi bi-calendar-check" aria-hidden="true"></i>
                                            <input type="date" name="date_to" id="date-to-input" class="form-control" value="{{ request('date_to') }}" aria-label="Date de fin">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="public-search-field">
                                            <i class="bi bi-sort-down" aria-hidden="true"></i>
                                            <select name="sort" id="sort-input" class="form-select" aria-label="Trier les offres">
                                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récentes</option>
                                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Plus anciennes</option>
                                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Titre A-Z</option>
                                                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Titre Z-A</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100" id="reset-filters" style="min-height: 3rem;">
                                            <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>Réinitialiser
                                        </button>
                                    </div>
                                </div>

                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none filter-toggle" id="toggle-advanced-filters">
                                        <i class="bi bi-sliders me-1" aria-hidden="true"></i>Filtres avancés
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Offres -->
        <section class="public-section">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div>
                        <span class="public-eyebrow mb-2" id="total-offers-badge">
                            <i class="bi bi-collection" aria-hidden="true"></i>
                            {{ $offers->total() }} offre{{ $offers->total() > 1 ? 's' : '' }} disponible{{ $offers->total() > 1 ? 's' : '' }}
                        </span>
                        <h2 class="h3 fw-bold mb-0">Toutes les offres</h2>
                    </div>
                    <span class="text-secondary small">
                        <i class="bi bi-lightning-charge me-1" aria-hidden="true"></i>
                        Mise à jour en continu
                    </span>
                </div>

                <div id="offers-container" class="offers-container">
                    @if($offers->count() > 0)
                        <div class="row g-3" id="offers-grid">
                            @foreach($offers as $offer)
                                @include('offers._card', ['offer' => $offer])
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4" id="pagination-container">
                            {{ $offers->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="public-empty-state" id="empty-state">
                            <span class="public-empty-icon"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-semibold">Aucune offre trouvée</h3>
                            <p class="text-secondary mb-3">Essayez de modifier vos critères de recherche.</p>
                            <a href="{{ route('offers.index') }}" class="btn btn-outline-primary">Réinitialiser les filtres</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')

    <script>
        window.OffersConfig = {
            searchUrl: '{{ route('offers.search') }}',
            indexUrl: '{{ route('offers.index') }}',
        };
    </script>

    @push('scripts')
    <script src="{{ asset('js/offers-search.js') }}"></script>
    @endpush
    @stack('scripts')
</body>
</html>