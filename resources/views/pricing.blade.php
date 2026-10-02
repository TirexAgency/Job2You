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
                        <a class="nav-link" href="#">Offres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('pricing') }}">Tarifs</a>
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
            <h1 class="display-5 fw-bold mb-3" style="letter-spacing: -0.025em;">Tarifs simples et transparents</h1>
            <p class="lead text-secondary mb-0 mx-auto" style="max-width: 600px;">Choisissez la formule adaptée à vos besoins. Sans engagement, résiliable à tout moment.</p>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <!-- Gratuit -->
                <div class="col-md-4">
                    <div class="pricing-card">
                        <h3 class="h5 fw-semibold mb-1">Gratuit</h3>
                        <p class="text-secondary small mb-3">Pour découvrir la plateforme</p>
                        <div class="price mb-3">0<span>Ar/mois</span></div>
                        <ul class="feature-list mb-4">
                            <li><i class="bi bi-check-circle"></i> Accès aux offres</li>
                            <li><i class="bi bi-check-circle"></i> Recherche basique</li>
                            <li><i class="bi bi-check-circle"></i> Profil candidat</li>
                            <li><i class="bi bi-check-circle"></i> 3 alertes SMS/mois</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline w-100">Commencer gratuitement</a>
                    </div>
                </div>

                <!-- Pro -->
                <div class="col-md-4">
                    <div class="pricing-card popular">
                        <span class="popular-badge">Populaire</span>
                        <h3 class="h5 fw-semibold mb-1">Pro</h3>
                        <p class="text-secondary small mb-3">Pour les candidats actifs</p>
                        <div class="price mb-3">85 000<span>Ar/mois</span></div>
                        <ul class="feature-list mb-4">
                            <li><i class="bi bi-check-circle"></i> Tout le plan Gratuit</li>
                            <li><i class="bi bi-check-circle"></i> Matching avancé</li>
                            <li><i class="bi bi-check-circle"></i> Alertes SMS illimitées</li>
                            <li><i class="bi bi-check-circle"></i> Score de compatibilité</li>
                            <li><i class="bi bi-check-circle"></i> Support prioritaire</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-primary w-100">Choisir Pro</a>
                    </div>
                </div>

                <!-- Entreprise -->
                <div class="col-md-4">
                    <div class="pricing-card">
                        <h3 class="h5 fw-semibold mb-1">Entreprise</h3>
                        <p class="text-secondary small mb-3">Pour les recruteurs</p>
                        <div class="price mb-3">450 000<span>Ar/mois</span></div>
                        <ul class="feature-list mb-4">
                            <li><i class="bi bi-check-circle"></i> Publication d'offres</li>
                            <li><i class="bi bi-check-circle"></i> Accès à la CVthèque</li>
                            <li><i class="bi bi-check-circle"></i> Matching candidats</li>
                            <li><i class="bi bi-check-circle"></i> Analytics avancés</li>
                            <li><i class="bi bi-check-circle"></i> Support dédié</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline w-100">Contacter l'équipe</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-5">
        <div class="container">
            <h2 class="h3 fw-bold text-center mb-4">Questions fréquentes</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Puis-je changer de formule à tout moment ?
                                </button>
                            </h3>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary">
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
                                <div class="accordion-body text-secondary">
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
                                <div class="accordion-body text-secondary">
                                    Notre algorithme analyse votre profil (compétences, expérience, préférences) et le compare aux offres disponibles pour calculer un score de compatibilité en temps réel.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                        <li><a href="#" class="small">Offres d'emploi</a></li>
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
