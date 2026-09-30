<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Job2You

Job2You est une plateforme de recherche d'emploi qui met en relation les profils candidats et les offres adaptées à leurs compétences. Le projet centralise les offres, prépare un score de compatibilité et prévoit des alertes pour les opportunités pertinentes.

## Fonctionnalités

- inscription, connexion et réinitialisation du mot de passe ;
- vérification de l'adresse e-mail et protection des routes authentifiées ;
- gestion du profil utilisateur ;
- recherche et affichage des offres d'emploi ;
- gestion des profils candidats, compétences, sources et correspondances ;
- plans, abonnements, paiements et journalisation des SMS dans le modèle de données ;
- rôles `admin` et `candidate` avec middleware dédié.

Les écrans et flux de matching, de notifications et d'abonnement sont encore en cours d'implémentation. Les routes et migrations existantes servent de base au développement du MVP.

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
database/seeders/     Données initiales
resources/views/      Interfaces Blade
resources/js/         JavaScript et initialisation front-end
resources/css/        Styles de l'application
routes/               Routes web, authentification et console
tests/                Tests unitaires et fonctionnels
```

## Contribuer

1. Créer une branche dédiée à la modification.
2. Ajouter ou mettre à jour les tests concernés.
3. Vérifier `composer test` et `npm run build`.
4. Ouvrir une pull request en décrivant le comportement ajouté ou corrigé.

## Licence

Ce projet est distribué sous licence MIT.
