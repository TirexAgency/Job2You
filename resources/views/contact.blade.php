<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>Contact — {{ config('app.name', 'Job2You') }}</title>

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

        .contact-card {
            background: var(--surface-container-lowest);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        }

        .contact-info-card {
            background: var(--surface-container-lowest);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            text-align: center;
            height: 100%;
        }

        .contact-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: var(--surface-container);
            color: var(--primary) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin: 0 auto 1rem;
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
            <h1 class="display-5 fw-bold mb-3" style="letter-spacing: -0.025em;">Contactez-nous</h1>
            <p class="lead text-secondary mb-0 mx-auto" style="max-width: 600px;">Une question, une suggestion ou un partenariat ? Nous sommes à votre écoute.</p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="contact-card">
                        <h2 class="h4 fw-bold mb-4">Envoyez-nous un message</h2>
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nom complet</label>
                                    <input type="text" class="form-control" id="name" placeholder="Votre nom">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="votre@email.com">
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label">Sujet</label>
                                    <select class="form-select" id="subject">
                                        <option value="">Choisissez un sujet</option>
                                        <option value="support">Support technique</option>
                                        <option value="partnership">Partenariat</option>
                                        <option value="feedback">Suggestion</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" rows="5" placeholder="Décrivez votre demande..."></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i>
                                        <span>Envoyer le message</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="contact-info-card">
                                <div class="contact-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <h3 class="h6 fw-semibold mb-1">Email</h3>
                                <p class="text-secondary small mb-0">contact@job2you.com</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="contact-info-card">
                                <div class="contact-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <h3 class="h6 fw-semibold mb-1">Téléphone</h3>
                                <p class="text-secondary small mb-0">+225 07 00 00 00 00</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="contact-info-card">
                                <div class="contact-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <h3 class="h6 fw-semibold mb-1">Adresse</h3>
                                <p class="text-secondary small mb-0">Abidjan, Côte d'Ivoire</p>
                            </div>
                        </div>
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
