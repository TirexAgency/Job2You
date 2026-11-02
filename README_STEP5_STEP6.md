# Job2You - Étapes 5 & 6 : Recherche Offres & Moteur de Matching

## Résumé des implémentations

Cette branche (`feature/step5-step6-matching-search`) implémente les **Étapes 5 et 6** du planning MVP :

- **Étape 5 (S4-S5)** : Recherche et consultation des offres d'emploi
- **Étape 6 (S5)** : Moteur de matching candidat ↔ offre avec alertes SMS

---

## 🔍 Étape 5 : Recherche et consultation des offres

### Fonctionnalités implémentées

#### 1. Liste paginée des offres (Blade + Bootstrap 5)
- Affichage en grille responsive (col-md-6 col-lg-4)
- Cartes d'offres avec logo, titre, entreprise, localisation, type de contrat, compétences
- Pagination Bootstrap stylisée
- État vide élégant

#### 2. Recherche par mot-clé
- Champ texte : titre, entreprise, description
- Recherche temps réel via AJAX (debounce 300ms)
- Endpoint : `GET /offers/search`

#### 3. Filtres avancés (collapse)
- **Localisation** : texte libre
- **Type de contrat** : select dynamique (CDI, CDD, Freelance, Stage, etc.)
- **Date de publication** : date_from / date_to
- **Tri** : Plus récentes, Plus anciennes, Titre A-Z, Titre Z-A
- Bouton "Réinitialiser les filtres"

#### 4. Fiche offre détaillée
- Description complète
- Compétences requises (badges avec astérisque pour obligatoires)
- Informations : entreprise, localisation, contrat, date, source
- Lien direct vers l'offre source (bouton primaire)
- Offres similaires (3 max)

#### 5. Endpoints AJAX
| Endpoint | Méthode | Description |
|----------|---------|-------------|
| `/offers` | GET | Page complète (HTML) |
| `/offers/search` | GET | JSON pour AJAX (pagination, filtres, tri) |
| `/offers/{offer}` | GET | Fiche détail (HTML) |

#### 6. JavaScript (resources/js/offers-search.js)
- Recherche temps réel avec debounce
- Pagination infinie (scroll à 80%)
- Mise à jour URL via History API
- Gestion états de chargement (spinner)
- Toggle filtres avancés
- Fallback rechargement page si erreur AJAX

---

## 🎯 Étape 6 : Moteur de Matching

### Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    MatchingService                           │
├─────────────────────────────────────────────────────────────┤
│  scoreFor(profile, offer) → {score, details, meets_threshold}│
│  computeForProfile(profile) → int                           │
│  computeForOffer(offer) → int                               │
│  getRecommendations(userId, limit) → Collection             │
└─────────────────────────────────────────────────────────────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
    ┌──────────┐    ┌──────────┐    ┌──────────┐
    │ Settings │    │ JobMatch │    │  Queue   │
    │ (poids,  │    │ (score,  │    │ MatchNew │
    │  seuil)  │    │  details)│    │ OfferJob │
    └──────────┘    └──────────┘    └──────────┘
```

### Critères et pondérations (configurables via `settings` table)

| Critère | Clé setting | Poids défaut | Description |
|---------|-------------|--------------|-------------|
| Compétences | `matching.skills_weight` | 40% | Intersection compétences profil ↔ offre |
| Métier/Intitulé | `matching.job_weight` | 25% | Similarité texte (similar_text) |
| Localisation | `matching.location_weight` | 15% | Ville exacte, région, mobilité remote |
| Expérience | `matching.experience_weight` | 10% | Niveau junior/mid/senior/expert |
| Contrat | `matching.contract_weight` | 10% | Correspondance exacte CDI/CDD/etc. |

**Formule** : `score = Σ(poids × score_critère) ∈ [0, 1]`

**Seuil alerte SMS** : `matching.alert_threshold` (défaut 70%)

### Détail des algorithmes

#### 1. Compétences (40%)
```php
$matched = count(array_intersect($profileSkills, $offerSkills));
return $matched / count($offerSkills); // 0 à 1
```
- Si offre sans compétences → score neutre 0.5

#### 2. Métier/Intitulé (25%)
```php
similar_text(strtolower($desired_jobs), strtolower($offer_title), $percent);
return $percent / 100;
```

#### 3. Localisation (15%)
- Mobilité `remote`/`national`/`international` → 1.0
- Ville exacte dans localisation offre → 1.0
- Même région → 0.7
- Défaut → 0.3

#### 4. Expérience (10%)
- Inférence niveau depuis titre offre (junior/senior/lead/architect)
- Mapping : junior=1, mid=4, senior=7, expert=12 ans
- Score selon différence : 0an=1.0, 1an=0.85, 2ans=0.70, 3ans=0.55, 4ans=0.40, 5+=0.25

#### 5. Contrat (10%)
- Correspondance exacte → 1.0
- Profil "any"/"indifferent" → 0.8
- Mismatch → 0.0

### Job asynchrone : MatchNewOfferJob

**Déclencheur** : Après création d'une nouvelle offre dans `ScrapingService::persistOffer()`

**Queue** : `matching` (séparée de `scraping`)

**Traitement** :
1. Récupère l'offre
2. Vérifie statut `active`
3. Calcule matching contre TOUS les profils candidats
4. Crée/met à jour `JobMatch` (user_id, offer_id, score, details_json)

**Configuration job** :
- `tries = 3`
- `backoff = 30s`
- `timeout = 120s`
- Tags Horizon : `matching`, `offer:{id}`

### Configuration via Settings (admin)

Les poids et seuil sont stockés en base (`settings` table) et modifiables sans redéploiement :

```php
// Exemple modification poids
Setting::set('matching.skills_weight', '0.50', Setting::TYPE_DECIMAL);
Setting::set('matching.alert_threshold', '0.75', Setting::TYPE_DECIMAL);

// Invalidation cache auto via Setting::forget()
```

### Recommandations candidat

- Route : `/candidate/recommendations` (auth + role:candidate)
- Affiche les `JobMatch` où `score >= threshold`
- Trié par score décroissant
- Limite configurable (défaut 10)

---

## 🗄️ Migrations créées

| Migration | Description |
|-----------|-------------|
| `2026_10_20_061238_create_settings_table.php` | Table configuration (clé, valeur, type, groupe) |

### Seeder : `SettingSeeder`
Crée les paramètres par défaut :
- 5 poids matching (total = 1.0)
- Seuil alerte 0.70
- Limite SMS gratuit = 2
- Config scraping (timeout, max offres)

---

## 🧪 Tests unitaires

### MatchingServiceTest (18 tests, 47 assertions)
- ✅ Structure résultat (score, details, meets_threshold, threshold)
- ✅ Match parfait → score élevé
- ✅ Pas de compétences communes → score 0
- ✅ Offre sans compétences → score neutre 0.5
- ✅ Localisation : remote, même ville, ville différente
- ✅ Contrat : exact, any, mismatch
- ✅ Expérience : même niveau, junior vs senior
- ✅ Score borné [0, 1]
- ✅ Seuil configurable via settings
- ✅ Poids chargés depuis settings
- ✅ computeForProfile crée JobMatch
- ✅ computeForOffer match tous profils
- ✅ Recommandations filtrées par seuil

### OfferControllerTest (12 tests, 78 assertions)
- ✅ Index paginé (12 par page)
- ✅ Filtres : search, location, contract_type, date_range
- ✅ Contract types distincts pour select
- ✅ Endpoint `/offers/search` retourne JSON structuré
- ✅ Filtres sur endpoint JSON
- ✅ Pagination JSON (page, has_more_pages)
- ✅ Show avec compétences et offres similaires
- ✅ Seules offres actives retournées
- ✅ Tri : title_asc, title_desc

---

## 📁 Fichiers créés/modifiés

### Nouveaux fichiers
```
app/Models/Setting.php
app/Jobs/MatchNewOfferJob.php
database/migrations/2026_10_20_061238_create_settings_table.php
database/seeders/SettingSeeder.php
database/factories/CandidateProfileFactory.php
database/factories/SkillFactory.php
database/factories/OfferSkillFactory.php
resources/views/offers/_card.blade.php
resources/js/offers-search.js
tests/Unit/MatchingServiceTest.php
tests/Unit/OfferControllerTest.php
```

### Fichiers modifiés
```
app/Services/MatchingService.php           # Refactor complet + expérience + settings
app/Services/Scraping/ScrapingService.php  # Dispatch MatchNewOfferJob
app/Http/Controllers/OfferController.php   # AJAX endpoint + filtres date + tri
app/Models/Setting.php                     # set() avec label auto
database/seeders/DatabaseSeeder.php        # + SettingSeeder
database/seeders/AdminSeeder.php           # Phone length fix
routes/web.php                             # + offers.search route
resources/views/offers/index.blade.php     # Filtres avancés + AJAX + partial
resources/js/app.js                        # + import offers-search.js
```

---

## 🚀 Comment tester

### 1. Base de données
```bash
php artisan migrate:fresh --seed
```

### 2. Lancer les tests
```bash
# Tests matching + offres
./vendor/bin/phpunit tests/Unit/MatchingServiceTest.php tests/Unit/OfferControllerTest.php

# Tous les tests (1 erreur pré-existante sur ScrapeSourceJobTest)
./vendor/bin/phpunit
```

### 3. Interface web
```bash
php artisan serve
# Ouvrir http://localhost:8000/offers
```

**Scénarios à tester :**
1. Recherche "Laravel" → filtre temps réel
2. Filtre localisation "Paris" + contrat "CDI"
3. Filtre date (dernière semaine)
4. Tri par titre A-Z / Z-A
5. Ouvrir fiche offre → lien source + offres similaires
6. Scroll bas de page → chargement page suivante (AJAX)

### 4. Matching manuel
```bash
# Calculer matching pour un profil
php artisan tinker --execute '
$profile = App\Models\CandidateProfile::first();
$service = app(App\Services\MatchingService::class);
echo $service->computeForProfile($profile) . " matchings calculés";
'

# Voir recommandations
php artisan tinker --execute '
$user = App\Models\User::first();
$service = app(App\Services\MatchingService::class);
$recos = $service->getRecommendations($user->id);
foreach($recos as $r) echo $r->offer->title . " = " . $r->score . "\n";
'
```

### 5. Configuration poids/seuil
```bash
php artisan tinker --execute '
App\Models\Setting::set("matching.skills_weight", "0.50", App\Models\Setting::TYPE_DECIMAL);
App\Models\Setting::set("matching.alert_threshold", "0.75", App\Models\Setting::TYPE_DECIMAL);
echo "Poids skills: " . App\Models\Setting::get("matching.skills_weight");
echo "Seuil: " . App\Models\Setting::get("matching.alert_threshold");
'
```

---

## 🔧 Commandes utiles

```bash
# Vider le cache settings
php artisan tinker --execute "App\Models\Setting::forget('matching.alert_threshold');"

# Relancer matching pour toutes les offres
php artisan tinker --execute '
App\Models\Offer::active()->each(function($o) {
    app(App\Jobs\MatchNewOfferJob::class)->dispatch($o->id);
});
'

# Voir les jobs en attente
php artisan queue:work matching --once
```

---

## ✅ Checklist de validation

### Étape 5 - Recherche
- [x] Liste paginée Bootstrap responsive
- [x] Recherche mot-clé (titre/entreprise/description)
- [x] Filtre localisation
- [x] Filtre type contrat (select dynamique)
- [x] Filtre date (from/to)
- [x] Tri (récent, ancien, titre)
- [x] Endpoint AJAX `/offers/search` retourne JSON
- [x] Recherche temps réel (debounce)
- [x] Pagination infinie (scroll)
- [x] Fiche offre avec lien source
- [x] Offres similaires
- [x] Tests unitaires (12 tests)

### Étape 6 - Matching
- [x] MatchingService avec 5 critères pondérés
- [x] Poids configurables via Settings
- [x] Seuil alerte configurable
- [x] Algorithme compétences (intersection)
- [x] Algorithme métier (similar_text)
- [x] Algorithme localisation (ville/région/remote)
- [x] Algorithme expérience (inférence titre + mapping)
- [x] Algorithme contrat (exact/any/mismatch)
- [x] Job MatchNewOfferJob (queue matching)
- [x] Dispatch auto après scraping nouvelle offre
- [x] Recommandations candidat (score >= seuil)
- [x] Tests unitaires complets (18 tests)

---

## 📝 Prochaines étapes (S7-S10)

| Étape | Description | Dépendances |
|-------|-------------|-------------|
| S7 | API SMS (Tirex), 2 SMS gratuits, quotas, queue, historique | Matching ✅ |
| S8 | Plans, abonnements, intégration FiveOnePay | Settings ✅ |
| S9 | Upload CV Premium, parsing IA, validation/correction | Settings ✅ |
| S10 | Tests complets, sécurité, déploiement, doc | Tout |

---

*Branche : `feature/step5-step6-matching-search`*  
*Dernière mise à jour : 2026-10-20*