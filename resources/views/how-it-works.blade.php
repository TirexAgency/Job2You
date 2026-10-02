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
                        <a class="nav-link" href="{{ route('pricing') }}">Tarifs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('how-it-works') }}">Comment ça marche</a>
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
            <h1 class="display-5 fw-bold mb-3" style="letter-spacing: -0.025em;">Comment ça marche</h1>
            <p class="lead text-secondary mb-0 mx-auto" style="max-width: 600px;">Job2You connecte les talents aux opportunités grâce à un matching intelligent et des alertes en temps réel.</p>
        </div>
    </section>

    <!-- Steps -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-2">Créez votre profil</h3>
                        <p class="text-secondary mb-0">Inscrivez-vous et complétez votre profil avec vos compétences, votre expérience et vos préférences.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <i class="bi bi-cpu"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-2">Matching intelligent</h3>
                        <p class="text-secondary mb-0">Notre algorithme analyse votre profil et le compare aux offres pour trouver les meilleures correspondances.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-2">Recevez des alertes</h3>
                        <p class="text-secondary mb-0">Soyez notifié par SMS dès qu'une offre compatible est publiée. Ne manquez plus jamais une opportunité.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="h3 fw-bold mb-3">Prêt à trouver votre prochain emploi ?</h2>
                    <p class="text-secondary mb-4">Rejoignez Job2You et laissez notre algorithme travailler pour vous.</p>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-rocket-takeoff"></i>
                        <span>Commencer maintenant</span>
                    </a>
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
