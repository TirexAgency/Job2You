<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Job2You') }} — Plateforme de matching d'offres d'emploi</title>
  
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #004ac6; --primary-container: #2563eb; --on-primary: #ffffff;
            --on-surface: #111c2d; --on-surface-variant: #434655;
            --surface: #f9f9ff; --surface-container-low: #f0f3ff;
            --surface-container: #e7eeff; --surface-container-high: #dee8ff;
            --surface-container-highest: #d8e3fb; --surface-container-lowest: #ffffff;
            --surface-variant: #d8e3fb; --outline: #737686; --outline-variant: #c3c6d7;
            --tertiary: #006329; --tertiary-fixed: #7ffc97;
            --error: #ba1a1a; --error-container: #ffdad6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--surface); color: var(--on-surface); font-size: 0.875rem; line-height: 1.375rem; -webkit-font-smoothing: antialiased; }
        ::-webkit-scrollbar { display: none; }
        .navbar { position: fixed; top: 0; left: 0; right: 0; height: 4rem; background: rgba(249, 249, 255, 0.9); backdrop-filter: blur(12px); box-shadow: 0 1px 8px rgba(0,0,0,0.04); z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; }
        .navbar-brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .navbar-brand img { height: 2rem; width: auto; }
        .navbar-brand span { font-size: 1.125rem; font-weight: 600; color: var(--primary); letter-spacing: -0.01em; }
        .navbar-nav { display: flex; align-items: center; gap: 1.5rem; }
        .navbar-nav a { font-size: 0.875rem; font-weight: 500; color: var(--on-surface-variant); text-decoration: none; transition: color 0.2s; }
        .navbar-nav a:hover { color: var(--on-surface); }
        .navbar-nav a.active { color: var(--primary); }
        .navbar-actions { display: flex; align-items: center; gap: 0.75rem; }
        .btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; background: var(--primary); color: var(--on-primary); font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .btn-primary:hover { background: var(--primary-container); }
        .btn-outline { display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; background: transparent; color: var(--on-surface); font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid var(--outline-variant); cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .btn-outline:hover { background: var(--surface-container); }
        .hero { position: relative; width: 100%; overflow: hidden; background: rgba(240, 243, 255, 0.7); padding: 8rem 2rem 4rem; }
        .hero-glow-1 { position: absolute; top: -8rem; left: -5rem; width: 24rem; height: 24rem; border-radius: 50%; background: rgba(0, 74, 198, 0.05); filter: blur(48px); pointer-events: none; }
        .hero-glow-2 { position: absolute; bottom: -8rem; right: -5rem; width: 24rem; height: 24rem; border-radius: 50%; background: rgba(64, 89, 170, 0.05); filter: blur(48px); pointer-events: none; }
        .hero-content { max-width: 48rem; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 10; display: flex; flex-direction: column; align-items: center; }
        .eyebrow { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.75rem; border-radius: 9999px; background: #dbe1ff; color: #003ea8; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.07); }
        .eyebrow i { font-size: 1rem; }
        .eyebrow span { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; }
        .hero h1 { font-size: 2.25rem; font-weight: 700; color: var(--on-surface); text-align: center; max-width: 40rem; letter-spacing: -0.025em; line-height: 2.75rem; }
        .hero p { font-size: 1rem; color: var(--on-surface-variant); text-align: center; max-width: 32rem; margin-top: 0.75rem; margin-bottom: 2rem; }
        .search-bar { width: 100%; max-width: 40rem; background: var(--surface-container-lowest); border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 0.75rem; margin-bottom: 2rem; }
        .search-form { display: flex; flex-direction: column; align-items: stretch; gap: 0.75rem; }
        .search-input-group { flex: 1; display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; background: var(--surface-container-low); border-radius: 0.5rem; transition: all 0.2s; }
        .search-input-group:focus-within { background: var(--surface-container-lowest); box-shadow: 0 1px 3px rgba(0,0,0,0.07); }
        .search-input-group i { font-size: 1.25rem; color: var(--outline); flex-shrink: 0; }
        .search-input-group input { background: transparent; border: none; outline: none; padding: 0; width: 100%; font-size: 0.875rem; color: var(--on-surface); }
        .search-input-group input::placeholder { color: var(--outline); }
        .search-divider { width: 1px; height: 2rem; background: var(--surface-container-highest); }
        .search-submit { display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; background: var(--primary); color: var(--on-primary); font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
        .search-submit:hover { background: var(--primary-container); }
        .quick-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 0.25rem; margin-top: 0.75rem; padding: 0 0.25rem; color: var(--on-surface-variant); }
        .quick-filters span { font-size: 0.75rem; font-weight: 600; color: var(--outline); }
        .quick-filters a { font-size: 0.75rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px; background: var(--surface-container); color: var(--primary); text-decoration: none; transition: all 0.2s; }
        .quick-filters a:hover { background: var(--surface-variant); }
        .features { width: 100%; max-width: 48rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 0.75rem; }
        .feature-card { display: flex; flex-direction: column; background: var(--surface-container-lowest); border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.07); transition: all 0.2s; }
        .feature-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .feature-icon { width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
        .feature-icon i { font-size: 1.5rem; }
        .feature-card h3 { font-size: 1.125rem; font-weight: 600; color: var(--on-surface); margin-bottom: 0.25rem; }
        .feature-card p { font-size: 0.875rem; color: var(--on-surface-variant); }
        .section { max-width: 48rem; margin: 0 auto; padding: 2rem; width: 100%; }
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 0.75rem; }
        .section-header h2 { font-size: 1.75rem; font-weight: 700; color: var(--on-surface); letter-spacing: -0.02em; }
        .badge { display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #dbe1ff; color: #003ea8; }
        .badge .dot { width: 0.5rem; height: 0.5rem; border-radius: 50%; background: var(--primary); margin-right: 0.25rem; display: inline-block; }
        .offers-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; }
        .offer-card { background: var(--surface-container-lowest); border-radius: 0.75rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.07); transition: all 0.2s; }
        .offer-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .offer-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
        .offer-logo { width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; object-fit: cover; }
        .offer-info h3 { font-size: 0.875rem; font-weight: 600; color: var(--on-surface); }
        .offer-info p { font-size: 0.8125rem; color: var(--on-surface-variant); }
        .offer-meta { display: flex; gap: 0.25rem; margin-bottom: 0.75rem; flex-wrap: wrap; }
        .offer-badge { display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: var(--surface-container); color: var(--on-surface); }
        .offer-skills { display: flex; gap: 0.25rem; margin-bottom: 0.75rem; flex-wrap: wrap; }
        .skill-badge { display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #e7f1ff; color: var(--primary); }
        .offer-footer { display: flex; align-items: center; justify-content: space-between; }
        .offer-score { font-size: 0.75rem; color: var(--tertiary); font-weight: 600; }
        .offer-link { color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500; }
        .offer-link:hover { text-decoration: underline; }
        .footer { width: 100%; background: var(--surface-container-low); box-shadow: 0 -1px 8px rgba(0,0,0,0.02); margin-top: 2rem; }
        .footer-content { max-width: 48rem; margin: 0 auto; padding: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
        .footer-section h4 { font-size: 0.875rem; font-weight: 500; color: var(--on-surface); margin-bottom: 0.75rem; }
        .footer-section ul { list-style: none; display: flex; flex-direction: column; gap: 0.25rem; }
        .footer-section a { font-size: 0.8125rem; color: var(--on-surface-variant); text-decoration: none; transition: color 0.2s; }
        .footer-section a:hover { color: var(--primary); }
        .footer-bottom { max-width: 48rem; margin: 0 auto; padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; color: var(--on-surface-variant); font-size: 0.75rem; font-weight: 600; }
        .footer-brand { display: flex; align-items: center; gap: 0.5rem; }
        .footer-brand img { height: 1.5rem; width: auto; }
        .footer-brand span { font-size: 1.125rem; font-weight: 600; color: var(--primary); }
        .footer-desc { font-size: 0.8125rem; color: var(--on-surface-variant); margin-top: 0.25rem; }
        .status { display: flex; align-items: center; gap: 0.25rem; color: var(--tertiary); font-size: 0.75rem; font-weight: 600; }
        .status i { font-size: 1rem; }
    </style>
</head>
<body>
    @include('partials.navbar-public')

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-glow-1"></div>
        <div class="hero-glow-2"></div>
        <div class="hero-content">
            <div class="eyebrow">
                <i class="bi bi-patch-check"></i>
                <span>Recrutement Tech & Métiers d'Avenir</span>
            </div>
            <h1>Trouvez votre prochain emploi</h1>
            <p>Des offres adaptées à votre profil, simplement.</p>

            <div class="search-bar">
                <form class="search-form" action="#" method="GET">
                    <div class="search-input-group">
                        <i class="bi bi-briefcase"></i>
                        <input type="text" placeholder="Métier ou compétence (ex: Développeur Laravel, DevOps...)">
                    </div>
                    <div class="search-divider"></div>
                    <div class="search-input-group">
                        <i class="bi bi-geo-alt"></i>
                        <input type="text" placeholder="Localisation (ex: Abidjan, Télétravail, Paris...)">
                    </div>
                    <button type="submit" class="search-submit">
                        <i class="bi bi-search"></i>
                        <span>Rechercher</span>
                    </button>
                </form>
                <div class="quick-filters">
                    <span>Populaires :</span>
                    <a href="">Laravel</a>
                    <a href="">Vue.js</a>
                    <a href="">Cloud AWS</a>
                    <a href="">Full-Remote</a>
                </div>
            </div>

            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon" style="background: #dbe1ff; color: var(--primary);">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <h3>Offres centralisées</h3>
                    <p>Toutes les opportunités réunies au même endroit pour une visibilité exhaustive du marché tech.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: var(--surface-container-high); color: var(--primary);">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <h3>Matching intelligent</h3>
                    <p>Score de compatibilité précis calculé en temps réel sur la base concrète de votre stack et expérience.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: var(--tertiary-fixed); color: var(--tertiary);">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h3>Alertes SMS</h3>
                    <p>Soyez notifié immédiatement dès qu'une offre hautement compatible paraît, sans vérifier vos courriels.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section offres récentes -->
    <section class="section">
        <div class="section-header">
            <div>
                <span class="badge">
                    <span class="dot"></span>
                    Offres récentes
                </span>
                <h2>Les dernières opportunités</h2>
            </div>
            <a href="" class="offer-link">
                Voir toutes les offres
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="offers-grid">
            <!-- Offre 1 -->
            <div class="offer-card">
                <div class="offer-header">
                    <img src="{{ asset('images/placeholder-company.svg') }}" alt="Entreprise" class="offer-logo">
                    <div class="offer-info">
                        <h3>Développeur Laravel Senior</h3>
                        <p>TechCorp — Abidjan</p>
                    </div>
                </div>
                <div class="offer-meta">
                    <span class="offer-badge">CDI</span>
                </div>
                <div class="offer-skills">
                    <span class="skill-badge">PHP</span>
                    <span class="skill-badge">Laravel</span>
                    <span class="skill-badge">MySQL</span>
                </div>
                <div class="offer-footer">
                    <span class="offer-score">95% compatible</span>
                    <a href="" class="offer-link">Voir l'offre</a>
                </div>
            </div>

            <!-- Offre 2 -->
            <div class="offer-card">
                <div class="offer-header">
                    <img src="{{ asset('images/placeholder-company.svg') }}" alt="Entreprise" class="offer-logo">
                    <div class="offer-info">
                        <h3>Frontend Developer React</h3>
                        <p>WebAgency — Yamoussoukro</p>
                    </div>
                </div>
                <div class="offer-meta">
                    <span class="offer-badge">Freelance</span>
                </div>
                <div class="offer-skills">
                    <span class="skill-badge">React</span>
                    <span class="skill-badge">JavaScript</span>
                </div>
                <div class="offer-footer">
                    <span class="offer-score">87% compatible</span>
                    <a href="" class="offer-link">Voir l'offre</a>
                </div>
            </div>

            <!-- Offre 3 -->
            <div class="offer-card">
                <div class="offer-header">
                    <img src="{{ asset('images/placeholder-company.svg') }}" alt="Entreprise" class="offer-logo">
                    <div class="offer-info">
                        <h3>Data Analyst Python</h3>
                        <p>DataFlow — Abidjan</p>
                    </div>
                </div>
                <div class="offer-meta">
                    <span class="offer-badge">CDD</span>
                </div>
                <div class="offer-skills">
                    <span class="skill-badge">Python</span>
                    <span class="skill-badge">SQL</span>
                </div>
                <div class="offer-footer">
                    <span class="offer-score">72% compatible</span>
                    <a href="" class="offer-link">Voir l'offre</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-brand">
                    <img src="{{ asset('images/teklab-logo.svg') }}" alt="TekLab Logo">
                    <span>TekLab</span>
                </div>
                <p class="footer-desc">Plateforme intelligente de matching et recrutement automatisé pour les talents et entreprises de la tech.</p>
            </div>
            <div class="footer-section">
                <h4>Plateforme</h4>
                <ul>
                    <li><a href="">Offres d'emploi</a></li>
                    <li><a href="">Formules & Tarifs</a></li>
                    <li><a href="{{ route('home') }}#fonctionnement">Fonctionnement</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Assistance & Légal</h4>
                <ul>
                    <li><a href="{{ route('home') }}#contact">Contact</a></li>
                    <li><a href="{{ route('home') }}#conditions">Conditions</a></li>
                    <li><a href="{{ route('home') }}#confidentialite">Confidentialité</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Statut</h4>
                <div class="status">
                    <i class="bi bi-check-circle"></i>
                    Système opérationnel
                </div>
                <p style="font-size: 0.8125rem; color: var(--on-surface-variant); margin-top: 0.25rem;">
                    Alertes SMS & Matching instantané actifs
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Job2You. Tous droits réservés.</p>
            <p>Conçu pour le recrutement tech à haute précision.</p>
        </div>
    </footer>
</body>
</html>
