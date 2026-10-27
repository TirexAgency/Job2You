<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>Tarifs — {{ config('app.name', 'Job2You') }}</title>

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

        .pricing-card {
            background: var(--surface-container-lowest);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            transition: all 0.2s;
            height: 100%;
            border: 2px solid transparent;
        }

        .pricing-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            transform: translateY(-4px);
        }

        .pricing-card.popular {
            border-color: var(--primary) !important;
            position: relative;
        }

        .popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: var(--on-primary);
            padding: 0.25rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--on-surface);
        }

        .price span {
            font-size: 1rem;
            font-weight: 400;
            color: var(--on-surface-variant) !important;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
            color: var(--on-surface-variant) !important;
        }

        .feature-list li i {
            color: var(--tertiary);
            font-size: 1.25rem;
        }

        .hero {
            background: rgba(240, 243, 255, 0.7);
            padding-top: 8rem;
            padding-bottom: 4rem;
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
            <div class="container text-center">
                <span class="public-eyebrow"><i class="bi bi-tags-fill" aria-hidden="true"></i>Tarifs transparents</span>
                <h1 class="public-hero-title">Investissez dans votre <span class="text-gradient">carrière</span>.</h1>
                <p class="public-hero-lead mx-auto">Des formules simples, sans engagement, pour les candidats comme pour les recruteurs.</p>
            </div>
        </section>

        <!-- Formules -->
        <section class="public-section">
            <div class="container">
                <div class="row g-4 justify-content-center align-items-stretch">
                    <!-- Gratuit -->
                    <div class="col-md-6 col-lg-4">
                        <div class="public-price-card">
                            <span class="public-price-icon"><i class="bi bi-stars" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-bold mb-1">Gratuit</h3>
                            <p class="text-secondary small mb-3">Pour découvrir la plateforme</p>
                            <div class="public-price">0<small>Ar / mois</small></div>
                            <ul class="public-feature-list">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Accès à toutes les offres</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Recherche et filtres basiques</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Profil candidat complet</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>3 alertes SMS par mois</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Commencer gratuitement</a>
                        </div>
                    </div>

                    <!-- Pro -->
                    <div class="col-md-6 col-lg-4">
                        <div class="public-price-card popular">
                            <span class="public-price-badge">Le plus choisi</span>
                            <span class="public-price-icon"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-bold mb-1">Pro</h3>
                            <p class="text-secondary small mb-3">Pour les candidats actifs</p>
                            <div class="public-price">85 000<small>Ar / mois</small></div>
                            <ul class="public-feature-list">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Tout le plan Gratuit</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Matching avancé et score détaillé</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Alertes SMS illimitées</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Recommandations prioritaires</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Support prioritaire</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-primary w-100">Choisir Pro</a>
                        </div>
                    </div>

                    <!-- Entreprise -->
                    <div class="col-md-6 col-lg-4">
                        <div class="public-price-card">
                            <span class="public-price-icon"><i class="bi bi-buildings" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-bold mb-1">Entreprise</h3>
                            <p class="text-secondary small mb-3">Pour les recruteurs</p>
                            <div class="public-price">450 000<small>Ar / mois</small></div>
                            <ul class="public-feature-list">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Publication d'offres illimitée</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Accès à la CVthèque</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Matching candidats avancé</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Analytics et rapports</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Accompagnement dédié</li>
                            </ul>
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary w-100">Contacter l'équipe</a>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 text-secondary small">
                    <span><i class="bi bi-shield-check me-1 text-success" aria-hidden="true"></i>Paiement sécurisé</span>
                    <span><i class="bi bi-arrow-repeat me-1 text-success" aria-hidden="true"></i>Sans engagement</span>
                    <span><i class="bi bi-headset me-1 text-success" aria-hidden="true"></i>Support réactif</span>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="public-section public-section-alt">
            <div class="container">
                <div class="public-section-heading text-center">
                    <span class="public-eyebrow"><i class="bi bi-question-circle-fill" aria-hidden="true"></i>FAQ</span>
                    <h2>Questions fréquentes</h2>
                    <p>Tout ce qu'il faut savoir avant de choisir votre formule.</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="accordion public-faq" id="faqAccordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Puis-je changer de formule à tout moment ?
                                    </button>
                                </h3>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Oui, vous pouvez passer à une formule supérieure ou inférieure à tout moment. Le changement est effectif immédiatement et nous ajustons la facturation au prorata.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Y a-t-il un engagement ?
                                    </button>
                                </h3>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Non, tous nos plans sont sans engagement. Vous pouvez résilier votre abonnement à tout moment depuis votre espace personnel.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Comment fonctionne le matching ?
                                    </button>
                                </h3>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Notre algorithme analyse votre profil (compétences, expérience, préférences) et le compare aux offres disponibles pour calculer un score de compatibilité en temps réel.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section pt-0">
            <div class="container">
                <div class="public-cta-band">
                    <h2>Une question sur nos formules ?</h2>
                    <p class="mb-4">Notre équipe vous aide à choisir le plan le plus adapté à votre projet.</p>
                    <a href="{{ route('contact') }}" class="btn btn-light btn-lg fw-semibold">
                        <i class="bi bi-envelope me-1" aria-hidden="true"></i>Nous contacter
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
