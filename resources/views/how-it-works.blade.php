<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>Comment ça marche — {{ config('app.name', 'Job2You') }}</title>

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

        .step-card {
            background: var(--surface-container-lowest);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            text-align: center;
            height: 100%;
            transition: all 0.2s;
        }

        .step-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            transform: translateY(-4px);
        }

        .step-number {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: var(--primary);
            color: var(--on-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        .step-icon {
            font-size: 2.5rem;
            color: var(--primary) !important;
            margin-bottom: 1rem;
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
        <!-- Hero -->
        <section class="public-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="public-eyebrow"><i class="bi bi-compass-fill" aria-hidden="true"></i>Le parcours Job2You</span>
                        <h1 class="public-hero-title">De votre profil à <span class="text-gradient">l'opportunité</span>.</h1>
                        <p class="public-hero-lead">Trois étapes simples pour recevoir des offres réellement adaptées à votre parcours, sans perdre de temps.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-1" aria-hidden="true"></i>Créer mon profil
                            </a>
                            <a href="{{ route('offers.index') }}" class="btn btn-outline-primary btn-lg">Voir les offres</a>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="public-hero-preview">
                            <div class="preview-kicker">
                                <span><i class="bi bi-activity me-1" aria-hidden="true"></i>Votre parcours</span>
                                <span class="text-info">Automatisé</span>
                            </div>

                            <div class="preview-score">
                                <span class="preview-score-ring"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
                                <div>
                                    <strong class="d-block text-white">Profil complété à 100%</strong>
                                    <small>Le matching peut maintenant travailler.</small>
                                </div>
                            </div>

                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-person-check" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>1 · Créez votre profil</strong>
                                    <small>Compétences, expérience, préférences</small>
                                </div>
                            </div>
                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-cpu" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>2 · Le matching analyse</strong>
                                    <small>Score calculé en temps réel</small>
                                </div>
                            </div>
                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>3 · Vous recevez vos alertes</strong>
                                    <small>SMS dès qu'une offre correspond</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Étapes -->
        <section class="public-section">
            <div class="container">
                <div class="public-section-heading text-center">
                    <span class="public-eyebrow"><i class="bi bi-signpost-split-fill" aria-hidden="true"></i>Simple par design</span>
                    <h2>Comment ça marche</h2>
                    <p>Un parcours fluide, de l'inscription à votre prochaine opportunité.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="public-step-card">
                            <span class="public-step-number">01</span>
                            <div class="public-step-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></div>
                            <h3 class="h5 fw-bold mb-2">Créez votre profil</h3>
                            <p class="text-secondary mb-0">Inscrivez-vous et complétez vos compétences, votre expérience et vos préférences professionnelles.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="public-step-card">
                            <span class="public-step-number">02</span>
                            <div class="public-step-icon"><i class="bi bi-cpu" aria-hidden="true"></i></div>
                            <h3 class="h5 fw-bold mb-2">Le matching analyse</h3>
                            <p class="text-secondary mb-0">Notre algorithme compare votre profil aux offres disponibles et calcule un score de compatibilité transparent.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="public-step-card">
                            <span class="public-step-number">03</span>
                            <div class="public-step-icon"><i class="bi bi-bell" aria-hidden="true"></i></div>
                            <h3 class="h5 fw-bold mb-2">Recevez vos alertes</h3>
                            <p class="text-secondary mb-0">Soyez prévenu par SMS dès qu'une offre fortement compatible est publiée. Ne manquez plus une opportunité.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Avantages -->
        <section class="public-section public-section-alt">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <div class="public-section-heading mb-0">
                            <span class="public-eyebrow"><i class="bi bi-stars" aria-hidden="true"></i>Pensé pour vous</span>
                            <h2>Votre temps mérite mieux que des candidatures à l'aveugle.</h2>
                            <p>Job2You met la technologie au service de votre projet professionnel.</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="public-feature-card">
                                    <div class="public-feature-icon"><i class="bi bi-bullseye" aria-hidden="true"></i></div>
                                    <h3>Score transparent</h3>
                                    <p>Comprenez pourquoi une offre vous correspond grâce à un score détaillé.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="public-feature-card">
                                    <div class="public-feature-icon"><i class="bi bi-lightning-charge" aria-hidden="true"></i></div>
                                    <h3>Alertes instantanées</h3>
                                    <p>Recevez les meilleures opportunités par SMS, sans actualiser votre boîte mail.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="public-feature-card">
                                    <div class="public-feature-icon"><i class="bi bi-shield-check" aria-hidden="true"></i></div>
                                    <h3>Données maîtrisées</h3>
                                    <p>Votre profil reste privé et vous contrôlez les informations partagées.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="public-feature-card">
                                    <div class="public-feature-icon"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></div>
                                    <h3>Recommandations utiles</h3>
                                    <p>Des offres ciblées plutôt qu'une liste interminable à parcourir.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="public-section">
            <div class="container">
                <div class="public-cta-band">
                    <h2>Prêt à trouver votre prochain emploi ?</h2>
                    <p class="mb-4">Rejoignez Job2You et laissez notre algorithme travailler pour vous.</p>
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-semibold">
                        <i class="bi bi-rocket-takeoff me-1" aria-hidden="true"></i>Commencer maintenant
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
