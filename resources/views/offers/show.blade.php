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
<body class="d-flex flex-column min-vh-100">
    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <!-- En-tête de l'offre -->
        <section class="public-hero pb-4">
            <div class="container">
                <a href="{{ route('offers.index') }}" class="d-inline-flex align-items-center gap-1 mb-3 text-decoration-none text-secondary">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Retour aux offres</span>
                </a>

                <div class="row align-items-end g-4">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start gap-3">
                            <x-company-avatar :name="$offer->company" />

                            <div class="min-width-0">
                                <h1 class="public-hero-title fs-2 mb-2">{{ $offer->title }}</h1>
                                <p class="mb-0 text-secondary">
                                    <i class="bi bi-building me-1" aria-hidden="true"></i>{{ $offer->company }}
                                    @if($offer->source)
                                        <span class="mx-2">·</span>{{ $offer->source->name }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @if($offer->contract_type)
                                <span class="public-offer-type">{{ $offer->contract_type }}</span>
                            @endif
                            <span class="public-skill"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>{{ $offer->location ?: 'Localisation non précisée' }}</span>
                            <span class="public-skill"><i class="bi bi-calendar me-1" aria-hidden="true"></i>Publié {{ $offer->published_at?->locale('fr')->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Postuler
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenu -->
        <section class="public-section pt-4">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="app-card">
                            <div class="app-card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="app-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></span>
                                        <h2 class="h4 fw-bold mb-0">Description du poste</h2>
                                    </div>
                                    @if(filled($offer->description))
                                        <span class="badge rounded-pill bg-light text-secondary border">
                                            <i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $readingMinutes }} min de lecture
                                        </span>
                                    @endif
                                </div>

                                @if(filled($offer->description))
                                    <div class="offer-description-preview">
                                        <p class="offer-description-excerpt text-secondary">{{ $descriptionExcerpt }}</p>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#offerDescriptionModal">
                                            <i class="bi bi-arrows-angle-expand me-1" aria-hidden="true"></i>Voir la description complète
                                        </button>
                                        <span class="small text-secondary">
                                            <i class="bi bi-list-check me-1" aria-hidden="true"></i>{{ $descriptionWordCount }} mots
                                        </span>
                                    </div>
                                @else
                                    <div class="app-empty-state py-4">
                                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                                        <p class="mb-2">La description détaillée n'est pas disponible pour cette offre.</p>
                                        <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Consulter l'annonce originale
                                        </a>
                                    </div>
                                @endif

                                @if($offer->offerSkills->count() > 0)
                                    <h3 class="h5 fw-bold mt-4 mb-3">Compétences requises</h3>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($offer->offerSkills as $offerSkill)
                                            <span class="public-skill {{ $offerSkill->required ? 'border-primary text-primary' : '' }}">
                                                {{ $offerSkill->skill->name }}
                                                @if($offerSkill->required)
                                                    <i class="bi bi-asterisk ms-1" aria-hidden="true"></i>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex flex-wrap align-items-center gap-3 mt-4 pt-3 border-top">
                                    <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="btn btn-primary">
                                        <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Postuler sur le site de l'entreprise
                                    </a>
                                    <span class="small text-secondary">
                                        <i class="bi bi-shield-check me-1" aria-hidden="true"></i>Vous serez redirigé vers le site source.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="app-card">
                            <div class="app-card-header">
                                <h3 class="app-section-title">Informations</h3>
                            </div>
                            <div class="app-card-body">
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex gap-3">
                                        <span class="app-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-building" aria-hidden="true"></i></span>
                                        <div>
                                            <small class="text-secondary d-block">Entreprise</small>
                                            <span class="fw-semibold">{{ $offer->company }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <span class="app-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                                        <div>
                                            <small class="text-secondary d-block">Localisation</small>
                                            <span class="fw-semibold">{{ $offer->location ?: 'Non précisée' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <span class="app-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-file-text" aria-hidden="true"></i></span>
                                        <div>
                                            <small class="text-secondary d-block">Type de contrat</small>
                                            <span class="fw-semibold">{{ $offer->contract_type ?: 'Non précisé' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <span class="app-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-calendar" aria-hidden="true"></i></span>
                                        <div>
                                            <small class="text-secondary d-block">Publié le</small>
                                            <span class="fw-semibold">{{ $offer->published_at?->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    @if($offer->source)
                                        <div class="d-flex gap-3">
                                            <span class="app-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-globe" aria-hidden="true"></i></span>
                                            <div>
                                                <small class="text-secondary d-block">Source</small>
                                                <span class="fw-semibold">{{ $offer->source->name }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Offres similaires -->
                @if($similarOffers->count() > 0)
                    <div class="mt-5">
                        <div class="public-section-heading">
                            <span class="public-eyebrow"><i class="bi bi-stars" aria-hidden="true"></i>À découvrir aussi</span>
                            <h2>Offres similaires</h2>
                        </div>
                        <div class="row g-3">
                            @foreach($similarOffers as $similarOffer)
                                @include('offers._card', ['offer' => $similarOffer])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        @if(filled($offer->description))
            <div class="modal fade offer-description-modal" id="offerDescriptionModal" tabindex="-1" aria-labelledby="offerDescriptionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header align-items-start gap-3">
                            <x-company-avatar :name="$offer->company" />

                            <div class="flex-grow-1 min-width-0">
                                <span class="public-eyebrow mb-1"><i class="bi bi-file-earmark-text" aria-hidden="true"></i>Description du poste</span>
                                <h2 class="modal-title h5 mb-1" id="offerDescriptionModalLabel">{{ $offer->title }}</h2>
                                <div class="d-flex flex-wrap gap-2 small text-secondary">
                                    <span><i class="bi bi-building me-1" aria-hidden="true"></i>{{ $offer->company }}</span>
                                    <span><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>{{ $offer->location ?: 'Non précisée' }}</span>
                                    @if($offer->contract_type)
                                        <span><i class="bi bi-file-text me-1" aria-hidden="true"></i>{{ $offer->contract_type }}</span>
                                    @endif
                                </div>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body">
                            <div class="offer-description-modal-meta">
                                <span><i class="bi bi-clock" aria-hidden="true"></i>{{ $readingMinutes }} min de lecture</span>
                                <span><i class="bi bi-list-check" aria-hidden="true"></i>{{ $descriptionWordCount }} mots</span>
                                @if($offer->source)
                                    <span><i class="bi bi-globe" aria-hidden="true"></i>{{ $offer->source->name }}</span>
                                @endif
                            </div>

                            @include('offers._description', ['description' => $offer->description])
                        </div>

                        <div class="modal-footer">
                            <span class="small text-secondary me-auto d-none d-sm-inline">
                                <i class="bi bi-shield-check me-1" aria-hidden="true"></i>Vous serez redirigé vers le site source.
                            </span>
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Postuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>

    @include('partials.public-footer')
</body>
</html>
