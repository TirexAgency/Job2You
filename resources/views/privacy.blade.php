<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>Politique de confidentialité — {{ config('app.name', 'Job2You') }}</title>

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

        .content-card {
            background: var(--surface-container-lowest);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        }

        .content-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .content-card h2:first-child {
            margin-top: 0;
        }

        .content-card p {
            color: var(--on-surface-variant) !important;
            line-height: 1.7;
        }

        .content-card ul {
            color: var(--on-surface-variant) !important;
            line-height: 1.7;
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
    <!-- Navbar -->
    @include('partials.public-navbar')

    <main class="flex-grow-1">

    <!-- Hero -->
    <section class="public-hero">
        <div class="container text-center">
            <h1 class="display-5 fw-bold mb-3" style="letter-spacing: -0.025em;">Politique de confidentialité</h1>
            <p class="lead text-secondary mb-0 mx-auto" style="max-width: 600px;">Dernière mise à jour : janvier 2026</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="content-card">
                        <h2>1. Collecte des données</h2>
                        <p>Nous collectons les informations que vous nous fournissez lors de l'inscription et de l'utilisation de la plateforme :</p>
                        <ul>
                            <li>Informations de contact (nom, email, téléphone)</li>
                            <li>Informations professionnelles (compétences, expérience, CV)</li>
                            <li>Préférences de recherche (localisation, type de contrat)</li>
                            <li>Données d'utilisation (connexions, recherches, interactions)</li>
                        </ul>

                        <h2>2. Utilisation des données</h2>
                        <p>Vos données sont utilisées pour :</p>
                        <ul>
                            <li>Fournir et améliorer nos services de matching</li>
                            <li>Envoyer des alertes SMS et des notifications</li>
                            <li>Personnaliser votre expérience sur la plateforme</li>
                            <li>Assurer la sécurité et la prévention de la fraude</li>
                        </ul>

                        <h2>3. Partage des données</h2>
                        <p>Nous ne vendons pas vos données personnelles. Vos informations peuvent être partagées avec des entreprises uniquement dans le cadre du processus de recrutement, et uniquement si vous avez manifesté un intérêt pour leurs offres.</p>

                        <h2>4. Sécurité</h2>
                        <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles pour protéger vos données contre tout accès non autorisé, toute perte ou toute divulgation.</p>

                        <h2>5. Vos droits</h2>
                        <p>Conformément à la réglementation en vigueur, vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition concernant vos données personnelles. Pour exercer ces droits, contactez-nous à privacy@job2you.com.</p>

                        <h2>6. Cookies</h2>
                        <p>Nous utilisons des cookies pour améliorer votre expérience et analyser le trafic. Vous pouvez configurer votre navigateur pour refuser les cookies, mais certaines fonctionnalités pourraient ne plus être accessibles.</p>

                        <h2>7. Contact</h2>
                        <p>Pour toute question concernant cette politique, contactez-nous à l'adresse : privacy@job2you.com</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    </main>

    @include('partials.public-footer')
</body>
</html>
