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
            background: rgba(249, 249, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 8px rgba(0,0,0,0.04);
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
            padding: 0.75rem;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('logo.png') }}" alt="Job2You">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('offers.index') }}">Offres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pricing') }}">Tarifs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('how-it-works') }}">Comment ça marche</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2"></i>
                            <span>Mon espace</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link px-3">Connexion</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i>
                            <span>Créer un compte</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container text-center">
            <h1 class="display-5 fw-bold mb-3" style="letter-spacing: -0.025em;">Offres d'emploi</h1>
            <p class="lead text-secondary mb-4">Toutes les opportunités tech réunies au même endroit.</p>

            <div class="search-bar mb-4">
                <form action="{{ route('offers.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="search-input-group">
                                <i class="bi bi-briefcase"></i>
                                <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Métier, compétence..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="search-input-group">
                                <i class="bi bi-geo-alt"></i>
                                <input type="text" name="location" class="form-control border-0 bg-transparent" placeholder="Localisation..." value="{{ request('location') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="search-input-group">
                                <i class="bi bi-file-text"></i>
                                <select name="contract_type" class="form-select border-0 bg-transparent">
                                    <option value="">Type de contrat</option>
                                    @foreach($contractTypes as $type)
                                        <option value="{{ $type }}" {{ request('contract_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i>
                                <span>Rechercher</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Offres -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge mb-2">
                        <span class="dot"></span>
                        {{ $offers->total() }} offres disponibles
                    </span>
                    <h2 class="h3 fw-bold mb-0" style="letter-spacing: -0.02em;">Toutes les offres</h2>
                </div>
            </div>

            @if($offers->count() > 0)
                <div class="row g-3">
                    @foreach($offers as $offer)
                        <div class="col-md-6 col-lg-4">
                            <div class="offer-card">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <img src="{{ asset('images/placeholder-company.svg') }}" alt="{{ $offer->company }}" class="offer-logo">
                                    <div>
                                        <h3 class="h6 fw-semibold mb-0">{{ $offer->title }}</h3>
                                        <p class="small text-secondary mb-0">{{ $offer->company }} — {{ $offer->location }}</p>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <span class="offer-badge">{{ $offer->contract_type }}</span>
                                </div>
                                @if($offer->offerSkills->count() > 0)
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        @foreach($offer->offerSkills->take(3) as $offerSkill)
                                            <span class="skill-badge">{{ $offerSkill->skill->name }}</span>
                                        @endforeach
                                        @if($offer->offerSkills->count() > 3)
                                            <span class="skill-badge">+{{ $offer->offerSkills->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary small">
                                        <i class="bi bi-calendar"></i>
                                        {{ $offer->published_at->diffForHumans() }}
                                    </span>
                                    <a href="{{ route('offers.show', $offer) }}" class="offer-link">Voir l'offre</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $offers->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-secondary"></i>
                    <h3 class="h5 fw-semibold mt-3">Aucune offre trouvée</h3>
                    <p class="text-secondary">Essayez de modifier vos critères de recherche.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ asset('logo.png') }}" alt="Job2You" style="height: 2.5rem; width: 10rem; object-fit: cover; object-position: center 46%;">
                    </div>
                    <p class="small text-secondary">Plateforme intelligente de matching et recrutement automatisé pour les talents et entreprises de la tech.</p>
                </div>
                <div class="col-md-2">
                    <h4 class="h6 fw-medium mb-2">Plateforme</h4>
                    <ul class="list-unstyled d-flex flex-column gap-1">
                        <li><a href="{{ route('offers.index') }}" class="small">Offres d'emploi</a></li>
                        <li><a href="{{ route('pricing') }}" class="small">Formules & Tarifs</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="small">Fonctionnement</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h4 class="h6 fw-medium mb-2">Assistance & Légal</h4>
                    <ul class="list-unstyled d-flex flex-column gap-1">
                        <li><a href="{{ route('contact') }}" class="small">Contact</a></li>
                        <li><a href="{{ route('terms') }}" class="small">Conditions</a></li>
                        <li><a href="{{ route('privacy') }}" class="small">Confidentialité</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4 class="h6 fw-medium mb-2">Statut</h4>
                    <div class="d-flex align-items-center gap-1 text-success small fw-semibold">
                        <i class="bi bi-check-circle"></i>
                        Système opérationnel
                    </div>
                    <p class="small text-secondary mt-1 mb-0">Alertes SMS & Matching instantané actifs</p>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <p class="small text-secondary mb-0 fw-semibold">&copy; 2026 Job2You. Tous droits réservés.</p>
                <p class="small text-secondary mb-0 fw-semibold">Conçu pour le recrutement tech à haute précision.</p>
            </div>
        </div>
    </footer>
</body>
</html>
