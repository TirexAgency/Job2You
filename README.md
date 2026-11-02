<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Job2You

Job2You est une plateforme de recherche d'emploi qui met en relation les profils candidats et les offres adaptées à leurs compétences. Le projet centralise les offres, prépare un score de compatibilité et prévoit des alertes pour les opportunités pertinentes.

---

## Fonctionnalités

### 🔐 Authentification & Utilisateurs
- Inscription, connexion et réinitialisation du mot de passe
- Vérification de l'adresse e-mail et protection des routes authentifiées
- Gestion du profil utilisateur (candidat / admin)
- Rôles `admin` et `candidate` avec middleware dédié
- Attribution automatique d'un abonnement gratuit (2 SMS) à l'inscription
- Rate limiting sur les routes sensibles (inscription, vérification email)
- Protection CSRF native Laravel sur tous les formulaires
- Protection contre l'énumération d'emails (messages d'erreur génériques)

### 📋 Gestion des Offres d'Emploi
- **Scraping automatique** multi-sources (RSS, JSON-LD, HTML)
- Sources configurables via l'admin (Asako.mg, Tanitjobs, etc.)
- Collecte planifiée horaire via cron externe
- Déduplication intelligente par fingerprint (SHA256)
- Interface publique de recherche avec filtres (mot-clé, localisation, type contrat)
- Détail d'offre avec offres similaires

### 👤 Espace Candidat
- Profil complet (expériences, formations, compétences)
- Upload CV avec parsing IA (selon plan)
- Recommandations d'offres basées sur le matching de compétences
- Alertes SMS pour nouvelles offres correspondantes
- Gestion abonnements & paiements (Free / Premium)

### ⚙️ Administration
- Dashboard avec statistiques
- CRUD Utilisateurs, Sources, Plans, Abonnements, Paiements
- Journal d'activité (logs métier structurés + logs bruts)
- Gestion des sources de scraping (activation, test manuel, planning)
- Envoi SMS & suivi des quotas

---

## Stack Technique

| Composant | Version / Tech |
|-----------|----------------|
| **PHP** | 8.4+ |
| **Framework** | Laravel 13.x (`bootstrap/app.php`, pas de `Kernel.php`) |
| **Auth** | Laravel Breeze |
| **Base de données** | MySQL / MariaDB (prod) · SQLite (dev) |
| **Frontend** | Blade + Vite + Tailwind CSS + Alpine.js |
| **Queue** | Database (table `jobs`) + Supervisor worker |
| **Scheduler** | Laravel Schedule + cron externe (cron-job.org) |
| **Tests** | PHPUnit (216 tests) |
| **Qualité** | Laravel Pint |

---

## Architecture Scraping

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  cron-job.org   │────▶│  POST /api/cron  │────▶│ ScrapeAllSources│
│  (toutes les h) │     │  /scrape-all     │     │      Job        │
└─────────────────┘     └──────────────────┘     └────────┬────────┘
                                                          │
                    ┌─────────────────────────────────────┘
                    ▼
         ┌────────────────────────┐
         │   Queue "scraping"     │  (table jobs)
         │  (database driver)     │
         └───────────┬────────────┘
                     │
         ┌───────────┴───────────┐
         ▼                       ▼
┌─────────────────┐     ┌─────────────────┐
│ ScrapeSourceJob │     │ ScrapeSourceJob │
│  (source 1)     │     │  (source 2)     │
│  Asako.mg       │     │  Tanitjobs      │
└────────┬────────┘     └────────┬────────┘
         │                       │
         ▼                       ▼
┌─────────────────────────────────────────┐
│        ScrapingService                  │
│  • RssCollector (RSS/Atom standard)     │
│  • AsakoCollector (JSON-LD + Next.js)   │
│  • Normalisation + Déduplication        │
└─────────────────────────────────────────┘
```

### Collecteurs disponibles
| Collector Key | Source | Type |
|---------------|--------|------|
| `rss` | RSS/Atom générique | Standard |
| `rss_asako_mg` | Asako.mg | JSON-LD / Next.js |
| `rss_portaljob_mg` | Portaljob.mg | JSON-LD |
| `rss_tanitjobs` | Tanitjobs | RSS |
| `rss_emploi_mg` | Emploi.mg | RSS |
| `rss_madacareers` | Madacareers | RSS |
| `rss_jobmada` | Jobmada | RSS |

---

## Installation

### Prérequis
- PHP 8.4+ avec extensions Laravel
- Composer
- Node.js 18+ et npm
- MySQL / MariaDB (production)
- Supervisor (pour queue worker production)

### Développement Local
```bash
# Installation complète (dépendances + config + migrations + build)
composer run setup

# Ou manuellement :
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build

# Lancer serveur + Vite
composer run dev
# → http://localhost:8000
```

### Variables d'Environnement Production (`.env`)
```env
APP_NAME=Job2You
APP_ENV=production
APP_DEBUG=false
APP_URL=https://job2you.votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=job2you
DB_USERNAME=job2you_user
DB_PASSWORD=secure_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-fournisseur.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@votre-domaine.com"
MAIL_FROM_NAME="${APP_NAME}"

# Token pour cron externe (cron-job.org) - GÉNÉRER AVEC : php -r "echo bin2hex(random_bytes(32));"
CRON_TOKEN=votre_token_64_caracteres_hex

# SMS / Paiement / IA (optionnel)
SMS_API_URL=https://sms.tirex-agency.site/api/v1/send-sms
SMS_API_KEY=
SMS_SENDER=Job2You
SMS_API_TIMEOUT=15
FIVEONEPAY_API_URL=
CV_PARSER_API_URL=
```

---

## Déploiement Production

### 1. Fichiers à uploader (FileZilla)
| Fichier | Destination | Note |
|---------|-------------|------|
| `routes/api.php` | `/var/www/job2you/routes/api.php` | **Nouveau** |
| `bootstrap/app.php` | `/var/www/job2you/bootstrap/app.php` | Modifié |
| `config/app.php` | `/var/www/job2you/config/app.php` | Modifié |
| `.env.prod` → `.env` | `/var/www/job2you/.env` | **Renommer + vrai CRON_TOKEN** |
| `laravel-worker.conf` | `/var/www/job2you/laravel-worker.conf` | Pour référence |

### 2. Configuration Serveur (SSH requis)
```bash
cd /var/www/job2you

# Dépendances + optimisation
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Caches Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Base de données
php artisan migrate --force
php artisan db:seed --force  # Si première installation

# Lien storage public
php artisan storage:link
```

### 3. Supervisor (Queue Worker)
```bash
# Installer
sudo apt update && sudo apt install -y supervisor

# Config (adapter le chemin /var/www/job2you)
sudo cp laravel-worker.conf /etc/supervisor/conf.d/
sudo nano /etc/supervisor/conf.d/laravel-worker.conf

# Démarrer
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl status
# Doit afficher: laravel-queue-scraping RUNNING
```

### 4. Cron Scheduler (Laravel Schedule)
```bash
sudo crontab -u www-data -e
```
```cron
* * * * * cd /var/www/job2you && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Cron Externe (cron-job.org - Gratuit)
1. Créer compte sur [cron-job.org](https://cron-job.org)
2. Nouveau job :
   - **URL** : `https://votre-domaine.com/api/cron/scrape-all`
   - **Méthode** : `POST`
   - **Headers** :
     ```
     X-Cron-Token: VOTRE_CRON_TOKEN_DU_.ENV
     Content-Type: application/json
     ```
   - **Schedule** : `Every hour` (`0 * * * *`)
3. Activer → Le scraping tourne toutes les heures automatiquement

---

## Tests & Qualité

```bash
# Suite complète
composer test
# ou
php artisan test

# Unitaires seulement
php artisan test --testsuite=Unit

# Fonctionnels seulement
php artisan test --testsuite=Feature

# Filtrer
php artisan test --filter=test_scraping

# Formatage code (Pint)
vendor/bin/pint --dirty --format agent

# Build assets prod
npm run build
```

---

## Structure Projet (Points Clés)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── SourceController.php      # CRUD sources + scrape manuel
│   │   │   ├── ActivityLogController.php # Logs métier + bruts
│   │   │   └── DashboardController.php
│   │   ├── OfferController.php           # Public: liste + détail
│   │   └── Candidate/...                 # Profil, CV, matching
│   ├── Middleware/
│   │   └── CheckRole.php                 # Middleware 'role:admin|candidate'
│   └── Requests/
├── Jobs/
│   ├── ScrapeSourceJob.php               # Queue: scraping une source
│   └── ScrapeAllSourcesJob.php           # Queue: toutes sources actives
├── Models/
│   ├── Source.php                        # Sources de scraping
│   ├── Offer.php                         # Offres d'emploi
│   ├── User.php                          # Candidats + Admins
│   └── ... (Plan, Subscription, etc.)
├── Services/
│   ├── Scraping/
│   │   ├── ScrapingService.php           # Orchestrateur principal
│   │   └── Collectors/
│   │       ├── RssCollector.php          # RSS/Atom standard
│   │       └── AsakoCollector.php        # Asako.mg (JSON-LD)
│   └── ActivityLogger.php                # Logs structurés
└── Providers/
    └── AppServiceProvider.php
```

---

## Commandes Utiles

```bash
# Scraping manuel (dev/test)
php artisan tinker --execute='
$source = App\Models\Source::where("active", true)->first();
app(App\Services\Scraping\ScrapingService::class)->scrapeSource($source);
'

# Vider queue bloquée
php artisan queue:flush

# Voir jobs en attente
php artisan queue:monitor

# Nettoyage offres (expirer, archiver, dédupliquer)
php artisan scraping:cleanup --expire --archive=6 --deduplicate

# Logs activité (visibles aussi dans /admin/logs)
tail -f storage/logs/activity-*.log

# Worker logs (si supervisor)
tail -f storage/logs/worker-scraping.log
```

---

## Sécurité

| Risque | Mitigation | Statut |
|--------|-----------|--------|
| Force brute inscription | Rate limiting 5/min | ✅ |
| Force brute connexion | Rate limiting 5/min | ✅ |
| Énumération emails | Messages génériques | ✅ |
| CSRF | Middleware natif | ✅ |
| Injection SQL | Eloquent (bound params) | ✅ |
| XSS | Échappement Blade `{{ }}` | ✅ |
| Token vérification falsifié | `signed` + hash | ✅ |
| Vol session | `SESSION_ENCRYPT=true` | ⚠️ Configurer prod |
| Scraping abuse | Token cron + rate limit source | ✅ |

---

## Contribuer

1. Créer une branche dédiée (`feat/...`, `fix/...`)
2. Ajouter/mettre à jour les tests concernés
3. Vérifier `composer test` et `npm run build`
4. Formater avec `vendor/bin/pint --dirty --format agent`
5. Ouvrir une Pull Request

---

## Licence

Ce projet est distribué sous licence **MIT**.

---

## Documentation Connexe

| Fichier | Description |
|---------|-------------|
| `DEPLOY-CHECKLIST.md` | Checklist complète déploiement (FileZilla + SSH + cron) |
| `DEPLOY-QUEUE-WORKER.md` | Activation Supervisor + Queue worker + alternatives |
| `DEPLOY-SUPERVISOR.md` | Quick-start Supervisor |
| `laravel-worker.conf` | Config Supervisor prête à l'emploi |