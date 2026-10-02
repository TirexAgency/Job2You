<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>{{ $offer->title }} — {{ config('app.name', 'Job2You') }}</title>

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

        .offer-card {
            background: var(--surface-container-lowest);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        }

        .offer-logo {
            width: 4rem;
            height: 4rem;
            border-radius: 0.75rem;
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

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
            color: var(--on-surface-variant) !important;
        }

        .info-item i {
            color: var(--primary) !important;
            font-size: 1.25rem;
        }

        .similar-offer-card {
            background: var(--surface-container-lowest);
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            transition: all 0.2s;
            height: 100%;
        }

        .similar-offer-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <a href="{{ route('offers.index') }}" class="text-decoration-none text-secondary mb-3 d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i>
                        <span>Retour aux offres</span>
                    </a>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('images/placeholder-company.svg') }}" alt="{{ $offer->company }}" class="offer-logo">
                        <div>
                            <h1 class="h3 fw-bold mb-1">{{ $offer->title }}</h1>
                            <p class="text-secondary mb-0">{{ $offer->company }}</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="offer-badge">{{ $offer->contract_type }}</span>
                        <span class="offer-badge"><i class="bi bi-geo-alt"></i> {{ $offer->location }}</span>
                        <span class="offer-badge"><i class="bi bi-calendar"></i> Publié {{ $offer->published_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenu -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="offer-card">
                        <h2 class="h4 fw-bold mb-3">Description du poste</h2>
                        <p class="text-secondary" style="line-height: 1.8;">{{ $offer->description }}</p>

                        @if($offer->offerSkills->count() > 0)
                            <h3 class="h5 fw-semibold mt-4 mb-3">Compétences requises</h3>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($offer->offerSkills as $offerSkill)
                                    <span class="skill-badge {{ $offerSkill->required ? 'bg-danger text-white' : '' }}">
                                        {{ $offerSkill->skill->name }}
                                        @if($offerSkill->required)
                                            <i class="bi bi-asterisk ms-1"></i>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-top">
                            <a href="{{ $offer->source_url }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <span>Postuler sur le site de l'entreprise</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="offer-card">
                        <h3 class="h5 fw-semibold mb-3">Informations</h3>
                        <div class="info-item">
                            <i class="bi bi-building"></i>
                            <div>
                                <small class="text-secondary">Entreprise</small>
                                <p class="mb-0 fw-medium">{{ $offer->company }}</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-geo-alt"></i>
                            <div>
                                <small class="text-secondary">Localisation</small>
                                <p class="mb-0 fw-medium">{{ $offer->location }}</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-file-text"></i>
                            <div>
                                <small class="text-secondary">Type de contrat</small>
                                <p class="mb-0 fw-medium">{{ $offer->contract_type }}</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-calendar"></i>
                            <div>
                                <small class="text-secondary">Publié le</small>
                                <p class="mb-0 fw-medium">{{ $offer->published_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @if($offer->source)
                            <div class="info-item">
                                <i class="bi bi-globe"></i>
                                <div>
                                    <small class="text-secondary">Source</small>
                                    <p class="mb-0 fw-medium">{{ $offer->source->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Offres similaires -->
            @if($similarOffers->count() > 0)
                <div class="mt-5">
                    <h2 class="h4 fw-bold mb-4">Offres similaires</h2>
                    <div class="row g-3">
                        @foreach($similarOffers as $similarOffer)
                            <div class="col-md-4">
                                <div class="similar-offer-card">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <img src="{{ asset('images/placeholder-company.svg') }}" alt="{{ $similarOffer->company }}" style="width: 2rem; height: 2rem; border-radius: 0.5rem; object-fit: cover;">
                                        <div>
                                            <h3 class="h6 fw-semibold mb-0">{{ $similarOffer->title }}</h3>
                                            <p class="small text-secondary mb-0">{{ $similarOffer->company }} — {{ $similarOffer->location }}</p>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <span class="offer-badge">{{ $similarOffer->contract_type }}</span>
                                    </div>
                                    <a href="{{ route('offers.show', $similarOffer) }}" class="offer-link">Voir l'offre</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
