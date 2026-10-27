<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>{{ config('app.name', 'Job2You') }} — Plateforme de matching d'offres d'emploi</title>

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
            --surface-container-highest: #d8e3fb;
            --surface-container-lowest: #ffffff;
            --tertiary: #006329;
            --tertiary-fixed: #7ffc97;
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

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--on-primary);
        }

        .btn-primary:hover {
            background: var(--primary-container);
            border-color: var(--primary-container);
            color: var(--on-primary);
        }

        .btn-outline {
            background: transparent;
            color: var(--on-surface);
            border-color: #c3c6d7;
        }

        .btn-outline:hover {
            background: var(--surface-container);
            color: var(--on-surface);
        }

        .hero {
            background: rgba(240, 243, 255, 0.7);
            padding-top: 8rem;
            padding-bottom: 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero-glow-1 {
            position: absolute;
            top: -8rem;
            left: -5rem;
            width: 24rem;
            height: 24rem;
            border-radius: 50%;
            background: rgba(0, 74, 198, 0.05);
            filter: blur(48px);
            pointer-events: none;
        }

        .hero-glow-2 {
            position: absolute;
            bottom: -8rem;
            right: -5rem;
            width: 24rem;
            height: 24rem;
            border-radius: 50%;
            background: rgba(64, 89, 170, 0.05);
            filter: blur(48px);
            pointer-events: none;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            background: #dbe1ff;
            color: #003ea8;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
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

        .search-input-group input {
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

        .search-divider {
            width: 1px;
            height: 2rem;
            background: var(--surface-container-highest);
        }

        .quick-filters a {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            background: var(--surface-container);
            color: var(--primary);
            text-decoration: none;
            transition: all 0.2s;
        }

        .quick-filters a:hover {
            background: var(--surface-container-high);
        }

        .feature-card {
            background: var(--surface-container-lowest);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            transition: all 0.2s;
            height: 100%;
        }

        .feature-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .feature-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
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
            color: var(--primary);
        }

        .offer-score {
            font-size: 0.75rem;
            color: var(--tertiary);
            font-weight: 600;
        }

        .offer-link {
            color: var(--primary);
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

        .footer-brand img {
            height: 2.5rem;
            width: 10rem;
            object-fit: cover;
            object-position: center 46%;
        }

        .footer a {
            color: var(--on-surface-variant);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: var(--primary);
        }

        .status {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--tertiary);
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <!-- Hero -->
        <section class="public-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="public-eyebrow">
                            <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
                            Recrutement tech & métiers d'avenir
                        </span>

                        <h1 class="public-hero-title">
                            Trouvez <span class="text-gradient">l'emploi</span> qui vous ressemble.
                        </h1>

                        <p class="public-hero-lead">
                            Des offres sélectionnées pour votre profil, un score de compatibilité transparent et des alertes SMS dès qu'une opportunité vous correspond.
                        </p>

                        <div class="public-search-card">
                            <form action="{{ route('offers.index') }}" method="GET">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-5">
                                        <div class="public-search-field">
                                            <i class="bi bi-briefcase" aria-hidden="true"></i>
                                            <input type="text" name="search" class="form-control" placeholder="Métier ou compétence" aria-label="Métier ou compétence">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="public-search-field">
                                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                            <input type="text" name="location" class="form-control" placeholder="Ville ou télétravail" aria-label="Localisation">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary public-search-submit w-100">
                                            <i class="bi bi-search me-1" aria-hidden="true"></i>Rechercher
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="public-quick-links">
                                <span class="small fw-semibold text-secondary me-1">Populaires :</span>
                                <a href="{{ route('offers.index', ['search' => 'Laravel']) }}">Laravel</a>
                                <a href="{{ route('offers.index', ['search' => 'Vue.js']) }}">Vue.js</a>
                                <a href="{{ route('offers.index', ['search' => 'AWS']) }}">Cloud AWS</a>
                                <a href="{{ route('offers.index', ['location' => 'Télétravail']) }}">Full-remote</a>
                            </div>
                        </div>

                        <div class="public-hero-stats">
                            <div class="public-hero-stat">
                                <strong>{{ $activeOffersCount }}</strong>
                                <span>offres actives</span>
                            </div>
                            <div class="public-hero-stat">
                                <strong>1 min</strong>
                                <span>pour créer son profil</span>
                            </div>
                            <div class="public-hero-stat">
                                <strong>SMS</strong>
                                <span>alertes en temps réel</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="public-hero-preview">
                            <div class="preview-kicker">
                                <span><i class="bi bi-stars me-1" aria-hidden="true"></i>Votre matching</span>
                                <span class="text-info">Temps réel</span>
                            </div>

                            <div class="preview-score">
                                <span class="preview-score-ring">92%</span>
                                <div>
                                    <strong class="d-block text-white">Excellente correspondance</strong>
                                    <small>Développeur Full Stack · Antananarivo</small>
                                </div>
                            </div>

                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-briefcase" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>Développeur Laravel Senior</strong>
                                    <small>Fintech · CDI</small>
                                </div>
                                <span class="badge">96%</span>
                            </div>

                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-code-slash" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>Ingénieur Frontend Vue.js</strong>
                                    <small>Éditeur SaaS · Remote</small>
                                </div>
                                <span class="badge">88%</span>
                            </div>

                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-cloud" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>DevOps & Cloud AWS</strong>
                                    <small>E-commerce · Hybride</small>
                                </div>
                                <span class="badge">84%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Avantages -->
        <section class="public-section">
            <div class="container">
                <div class="public-section-heading text-center">
                    <span class="public-eyebrow"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>Pourquoi Job2You</span>
                    <h2>Moins de candidatures, plus de bonnes rencontres.</h2>
                    <p>Une plateforme pensée pour faire gagner du temps aux talents comme aux recruteurs.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="public-feature-card">
                            <div class="public-feature-icon"><i class="bi bi-diagram-3" aria-hidden="true"></i></div>
                            <h3>Offres centralisées</h3>
                            <p>Toutes les opportunités tech réunies au même endroit, avec des informations claires et à jour.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="public-feature-card">
                            <div class="public-feature-icon"><i class="bi bi-bullseye" aria-hidden="true"></i></div>
                            <h3>Matching intelligent</h3>
                            <p>Un score de compatibilité concret, calculé à partir de vos compétences, de votre expérience et de vos préférences.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="public-feature-card">
                            <div class="public-feature-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></div>
                            <h3>Alertes SMS</h3>
                            <p>Soyez prévenu immédiatement lorsqu'une offre fortement compatible est publiée. Plus besoin de veiller sur votre boîte mail.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Offres récentes -->
        <section class="public-section public-section-alt">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div class="public-section-heading mb-0">
                        <span class="public-eyebrow"><i class="bi bi-briefcase-fill" aria-hidden="true"></i>Offres récentes</span>
                        <h2>Les dernières opportunités</h2>
                        <p>Une sélection fraîchement publiée sur la plateforme.</p>
                    </div>
                    <a href="{{ route('offers.index') }}" class="btn btn-outline-primary">
                        Voir toutes les offres
                        <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>

                @if($recentOffers->count() > 0)
                    <div class="row g-3">
                        @foreach($recentOffers as $offer)
                            @include('offers._card', ['offer' => $offer])
                        @endforeach
                    </div>
                @else
                    <div class="public-empty-state">
                        <span class="public-empty-icon"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                        <h3 class="h5 fw-semibold">Aucune offre disponible</h3>
                        <p class="text-secondary mb-0">Revenez bientôt pour découvrir de nouvelles opportunités.</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- CTA -->
        <section class="public-section">
            <div class="container">
                <div class="public-cta-band">
                    <span class="public-eyebrow"><i class="bi bi-rocket-takeoff-fill" aria-hidden="true"></i>Prêt à décoller ?</span>
                    <h2>Votre prochain emploi commence ici.</h2>
                    <p class="mb-4">Créez votre profil en quelques minutes et laissez le matching travailler pour vous.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-semibold">
                            <i class="bi bi-person-plus me-1" aria-hidden="true"></i>Créer mon compte
                        </a>
                        <a href="{{ route('offers.index') }}" class="btn btn-outline-light btn-lg">
                            Explorer les offres
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
