<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Job2You

Job2You est une plateforme de recherche d'emploi qui met en relation les profils candidats et les offres adaptées à leurs compétences. Le projet centralise les offres, prépare un score de compatibilité et prévoit des alertes pour les opportunités pertinentes.

## Fonctionnalités

- Inscription, connexion et réinitialisation du mot de passe ;
- Vérification de l'adresse e-mail et protection des routes authentifiées ;
- Gestion du profil utilisateur ;
- Recherche et affichage des offres d'emploi ;
- Gestion des profils candidats, compétences, sources et correspondances ;
- Plans, abonnements, paiements et journalisation des SMS dans le modèle de données ;
- Rôles `admin` et `candidate` avec middleware dédié ;
- Attribution automatique d'un abonnement gratuit (2 SMS) à l'inscription (RG01) ;
- Rate limiting sur les routes sensibles (inscription, vérification email) ;
- Protection CSRF native Laravel sur tous les formulaires ;
- Protection contre l'énumération d'emails (messages d'erreur génériques).

## Stack technique

- PHP 8.3 ou supérieur ;
- Laravel 13 ;
- Laravel Breeze pour l'authentification ;
- SQLite par défaut, avec support MySQL, MariaDB, PostgreSQL et SQL Server ;
- Blade, Vite, Tailwind CSS et Alpine.js ;
- PHPUnit pour les tests.

## Installation

### Prérequis

- PHP 8.3+ avec les extensions requises par Laravel ;
- Composer ;
- Node.js et npm.

### Mise en place

Depuis la racine du projet :

```bash
composer run setup
```

Cette commande installe les dépendances PHP et JavaScript, crée `.env` depuis `.env.example`, génère la clé d'application, exécute les migrations et construit les assets front-end.

Pour une installation manuelle :

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
```

La configuration par défaut utilise SQLite. Pour l'utiliser explicitement, créez la base locale :

```bash
touch database/database.sqlite
```

## Développement

Lancer Laravel et Vite ensemble :

```bash
composer run dev
```

L'application est ensuite disponible sur `http://localhost:8000`.

Les données initiales des plans peuvent être chargées avec :

```bash
php artisan db:seed
```

## Tests et qualité

Lancer la suite PHPUnit :

```bash
composer test
```

Lancer uniquement les tests unitaires :

```bash
php artisan test --testsuite=Unit
```

Lancer uniquement les tests fonctionnels :

```bash
php artisan test --testsuite=Feature
```

Lancer un fichier de test spécifique :

```bash
php artisan test tests/Feature/Auth/RegistrationFlowTest.php
```

Lancer un test spécifique par nom :

```bash
php artisan test --filter=test_successful_registration_creates_user
```

Formater les fichiers PHP modifiés avec Laravel Pint :

```bash
vendor/bin/pint --dirty --format agent
```

Construire les assets de production :

```bash
npm run build
```

## Structure du projet

```text
app/                  Contrôleurs, modèles, services, policies et jobs
database/migrations/  Schéma utilisateurs, offres, compétences et abonnements
database/seeders/     Données initiales (plans free/premium)
resources/views/      Interfaces Blade
resources/js/         JavaScript et initialisation front-end
resources/css/        Styles de l'application
routes/               Routes web, authentification et console
tests/                Tests unitaires et fonctionnels
```

## Documentation technique

### Architecture d'authentification

```
┌─────────────────────────────────────────────────────────────┐
│                    PARCOURS D'INSCRIPTION                    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  GET /register  ──►  Formulaire (avec @csrf)                │
│       │                                                     │
│       ▼                                                     │
│  POST /register  ──►  RegisterRequest (validation)          │
│       │              ├─ name required                       │
│       │              ├─ email required|unique               │
│       │              ├─ phone required|unique|regex:MG      │
│       │              └─ password required|confirmed|strong   │
│       │                                                     │
│       ▼                                                     │
│  DB::transaction()                                          │
│       ├─ User::create()  (role=candidate, status=pending)   │
│       └─ Subscription::create()  (plan=free, sms=2)         │
│       │                                                     │
│       ▼                                                     │
│  Auth::login()  +  Event::Registered                        │
│       │                                                     │
│       ▼                                                     │
│  Redirect → /email/verify  (avec notification)              │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Middleware appliqués

| Route | Middleware |
|-------|-----------|
| `GET /register` | `guest` |
| `POST /register` | `guest`, `throttle:5,1` |
| `GET /login` | `guest` |
| `POST /login` | `guest`, `throttle:5,1` |
| `GET /email/verify` | `auth` |
| `GET /email/verify/{id}/{hash}` | `auth`, `signed`, `throttle:6,1` |
| `POST /email/verification-notification` | `auth`, `throttle:6,1` |
| `GET /dashboard` | `auth`, `verified` |
| `GET /profile` | `auth`, `verified` |
| `POST /logout` | `auth` |

### Rate limiting

| Route | Limite | Fenêtre |
|-------|--------|---------|
| `POST /register` | 5 | 1 minute |
| `POST /login` | 5 | 1 minute |
| `GET /email/verify/{id}/{hash}` | 6 | 1 minute |
| `POST /email/verification-notification` | 6 | 1 minute |

### Protection contre l'énumération

Tous les messages d'erreur d'authentification sont génériques :

- **Inscription** : "Ces identifiants sont déjà associés à un compte existant."
- **Connexion** : "Identifiants incorrects ou compte non vérifié."

Cela empêche un attaquant de déterminer si un email/phone est déjà enregistré.

### Modèle de données

```
users
├── id, name, email, phone, password
├── role (admin|candidate)
├── status (pending|active|suspended|inactive)
├── plan (free|premium)
├── sms_quota, sms_sent
└── email_verified_at

plans
├── id, name, price, duration_days
├── sms_quota, cv_parsing_enabled, active
└── ...

subscriptions
├── id, user_id, plan_id
├── starts_at, ends_at
├── sms_remaining
└── status (active|inactive|suspended)
```

### Variables d'environnement requises

```env
APP_NAME=Job2You
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@job2you.com"
MAIL_FROM_NAME="${APP_NAME}"

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## Déploiement

### Checklist de validation avant mise en production

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] `APP_KEY` généré et sécurisé
- [ ] Base de données configurée (MySQL/PostgreSQL recommandé)
- [ ] `MAIL_MAILER` configuré (SMTP/SendGrid/SES)
- [ ] `SESSION_DRIVER` sécurisé (database/redis)
- [ ] `CACHE_STORE` configuré (redis recommandé)
- [ ] Certificat HTTPS actif
- [ ] `php artisan config:cache` exécuté
- [ ] `php artisan route:cache` exécuté
- [ ] `php artisan view:cache` exécuté
- [ ] Tests : `php artisan test` → 100% passent
- [ ] `npm run build` exécuté

### Commandes de déploiement

```bash
# 1. Installer les dépendances
composer install --no-dev --optimize-autoloader

# 2. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 3. Exécuter les migrations
php artisan migrate --force

# 4. Charger les données initiales
php artisan db:seed --force

# 5. Construire les assets
npm ci
npm run build

# 6. Optimiser
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Lancer le serveur
php artisan serve --host=0.0.0.0 --port=8000
```

### Points de vigilance sécurité

| Risque | Mitigation |
|--------|-----------|
| Force brute inscription | Rate limiting 5/min |
| Force brute connexion | Rate limiting 5/min |
| Énumération d'emails | Messages d'erreur génériques |
| CSRF | Middleware natif Laravel |
| Injection SQL | Eloquent ORM (paramètres liés) |
| XSS | Échappement Blade `{{ }}` |
| Lien de vérification falsifié | Signature `signed` + hash `sha1` |
| Session fixation | `session()->regenerate()` |
| Mots de passe faibles | Règles `Password::defaults()` |
| Vol de session | `SESSION_ENCRYPT=true` en prod |

## Contribuer

1. Créer une branche dédiée à la modification.
2. Ajouter ou mettre à jour les tests concernés.
3. Vérifier `composer test` et `npm run build`.
4. Ouvrir une pull request en décrivant le comportement ajouté ou corrigé.

## Licence

Ce projet est distribué sous licence MIT.
