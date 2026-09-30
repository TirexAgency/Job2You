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

- PHP 8.4+ ;
- Laravel 11+ (approche `bootstrap/app.php`, pas de `Kernel.php`) ;
- Laravel Breeze pour l'authentification ;
- SQLite par défaut, avec support MySQL, MariaDB, PostgreSQL et SQL Server ;
- Blade, Vite, Tailwind CSS et Alpine.js ;
- PHPUnit pour les tests.

## Installation

### Prérequis

- PHP 8.4+ avec les extensions requises par Laravel ;
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

### Exécuter les tests

Lancer la suite PHPUnit complète :

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

### Couverture des tests

#### Tests unitaires (`tests/Unit/`)

| Test | Description |
|------|-------------|
| `test_password_is_hashed_on_creation` | Vérifie que le mot de passe est hashé à la création |
| `test_password_is_hashed_on_update` | Vérifie que le mot de passe est hashé à la mise à jour |
| `test_password_hash_uses_bcrypt` | Vérifie l'algorithme bcrypt |
| `test_email_must_be_unique` | Vérifie l'unicité de l'email en base |
| `test_phone_must_be_unique` | Vérifie l'unicité du téléphone en base |
| `test_has_active_subscription_*` | Vérifie la logique d'abonnement actif |
| `test_get_sms_quota_*` | Vérifie le quota SMS |

#### Tests fonctionnels (`tests/Feature/`)

| Test | Description |
|------|-------------|
| `test_successful_registration_creates_user` | Inscription réussie : utilisateur créé |
| `test_successful_registration_creates_subscription` | Inscription réussie : abonnement free créé |
| `test_successful_registration_logs_user_in` | Inscription réussie : utilisateur connecté |
| `test_successful_registration_dispatches_registered_event` | Inscription réussie : event Registered dispatché |
| `test_email_verification_notification_is_sent` | Email de validation envoyé |
| `test_duplicate_email_is_rejected` | Email dupliqué rejeté |
| `test_duplicate_phone_is_rejected` | Téléphone dupliqué rejeté |
| `test_register_is_rate_limited_after_5_attempts` | Rate limiting inscription (5/min) |
| `test_login_is_rate_limited_after_5_attempts` | Rate limiting connexion (5/min) |
| `test_email_can_be_verified_with_valid_token` | Validation email avec token valide |
| `test_email_cannot_be_verified_with_invalid_hash` | Validation email avec hash invalide |
| `test_email_cannot_be_verified_with_unsigned_url` | Validation email sans signature |
| `test_email_cannot_be_verified_with_expired_token` | Validation email avec token expiré |
| `test_verification_email_can_be_resent` | Renvoi email de vérification |
| `test_verification_email_is_actually_resent` | Renvoi email réel |
| `test_verification_email_not_resent_if_already_verified` | Pas de renvoi si déjà vérifié |
| `test_verification_notification_is_rate_limited` | Rate limiting renvoi email (6/min) |
| `test_email_verification_is_rate_limited` | Rate limiting vérification email (6/min) |

### Formater le code

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
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          # Inscription + vérification email
│   │   ├── ProfileController.php       # Gestion profil
│   │   └── Auth/                       # Contrôleurs Breeze (login, password)
│   ├── Middleware/
│   │   └── CheckRole.php              # Middleware 'role'
│   └── Requests/
│       ├── RegisterRequest.php        # Validation inscription
│       └── ProfileUpdateRequest.php
├── Models/
│   ├── User.php
│   ├── Plan.php
│   └── Subscription.php
├── Policies/
│   └── UserPolicy.php
└── Providers/
    └── AppServiceProvider.php

database/
├── factories/                         # UserFactory avec states
├── migrations/                        # 16 migrations
└── seeders/                           # PlanSeeder (free/premium)

routes/
├── web.php                            # Routes principales
├── auth.php                           # Routes Breeze (non chargées)
└── console.php

tests/
├── Unit/                              # Tests unitaires
└── Feature/                           # Tests fonctionnels
    └── Auth/                          # Tests authentification
```

## Documentation technique

### Connexion et déconnexion sécurisées

- `GET /login` affiche le formulaire Blade avec `@csrf`, le champ « Se souvenir de moi » et la récupération de mot de passe.
- `POST /login` est protégé par `guest` et `throttle:5,1`. `LoginRequest` valide l'email et le mot de passe, limite aussi les tentatives par email et adresse IP, et n'authentifie que les comptes actifs avec email vérifié.
- Les erreurs d'identifiants, comptes non vérifiés et comptes inactifs partagent un message générique. Après succès, Laravel régénère l'identifiant de session et le guard gère le cookie ainsi que le `remember_token` quand « Se souvenir de moi » est activé.
- La redirection respecte d'abord l'URL `intended`, puis utilise le dashboard réel de l'application, dont le contenu est adapté au rôle. Les routes de rôle distinctes restent des endpoints de test JSON.
- `POST /logout` exige `auth` et un jeton CSRF. Le guard déconnecte l'utilisateur et renouvelle son `remember_token` ; la session est invalidée, le jeton CSRF est régénéré, puis l'utilisateur est redirigé vers le login avec un message de succès.
- Le cookie persistant est géré par le guard Laravel (`remember_web_*`) ; l'application ne fabrique pas son nom manuellement.
- Le profil utilise les formulaires Bootstrap pour les données personnelles, le mot de passe et la suppression du compte.

Commandes utiles depuis la racine :

```bash
php artisan route:list --path=login
php artisan route:list --path=logout
php artisan test --compact tests/Feature/Auth/AuthenticationTest.php
php artisan test --compact tests/Feature/Auth/LogoutTest.php
php artisan test --compact tests/Feature/AuthSecurityTest.php
php artisan test --compact tests/Feature/ProfileTest.php
vendor/bin/pint --dirty --format agent
npm run build
```

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
| `GET /admin/dashboard` | `auth`, `role:admin` |
| `GET /candidate/dashboard` | `auth`, `role:candidate` |

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
- **Connexion** : "Identifiants incorrects ou compte non disponible."

Cela évite de divulguer si l'email existe, s'il est vérifié ou si le compte est actif.

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

## Déploiement

### Variables .env nécessaires

```env
APP_NAME=Job2You
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://job2you.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=job2you
DB_USERNAME=job2you_user
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS="hello@job2you.com"
MAIL_FROM_NAME="${APP_NAME}"

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Checklist de validation avant mise en production

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] `APP_KEY` généré et sécurisé
- [ ] Base de données configurée (MySQL/PostgreSQL recommandé)
- [ ] `MAIL_MAILER` configuré (SMTP/SendGrid/SES)
- [ ] `SESSION_DRIVER` sécurisé (database/redis)
- [ ] `SESSION_ENCRYPT=true`
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

| Risque | Mitigation | Statut |
|--------|-----------|--------|
| Force brute inscription | Rate limiting 5/min | ✅ Implémenté |
| Force brute connexion | Rate limiting 5/min | ✅ Implémenté |
| Énumération d'emails | Messages d'erreur génériques | ✅ Implémenté |
| CSRF | Middleware natif Laravel | ✅ Implémenté |
| Injection SQL | Eloquent ORM (paramètres liés) | ✅ Implémenté |
| XSS | Échappement Blade `{{ }}` | ✅ Implémenté |
| Lien de vérification falsifié | Signature `signed` + hash `sha1` | ✅ Implémenté |
| Session fixation | `session()->regenerate()` | ✅ Implémenté |
| Mots de passe faibles | Règles `Password::min(8)->mixedCase()->numbers()->symbols()` | ✅ Implémenté |
| Vol de session | `SESSION_ENCRYPT=true` en prod | ⚠️ À configurer |
| Token de vérification réutilisable | `temporarySignedRoute` avec expiration | ✅ Implémenté |

## Contribuer

1. Créer une branche dédiée à la modification.
2. Ajouter ou mettre à jour les tests concernés.
3. Vérifier `composer test` et `npm run build`.
4. Ouvrir une pull request en décrivant le comportement ajouté ou corrigé.

## Licence

Ce projet est distribué sous licence MIT.
