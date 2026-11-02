# Activation Queue Worker - Job2You

## 🎯 Problème
Le scraping est **asynchrone** (via queue `database`). Le bouton admin "Lancer la collecte" et le scheduler (toutes les 2 heures) dispatchent des jobs dans la table `jobs`, mais **aucun worker ne les traite** sans cette configuration.

---

## ✅ Solution : Supervisor (Recommandé)

### 1. Installer Supervisor (sur le serveur)
```bash
sudo apt update && sudo apt install -y supervisor
```

### 2. Copier la config
```bash
# Depuis le projet déployé
sudo cp /var/www/job2you/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Adapter le chemin si différent de /var/www/job2you
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

### 3. Config à adapter dans `laravel-worker.conf`
```ini
command=php /var/www/job2you/artisan queue:work database --queue=scraping --sleep=5 --tries=3 --max-time=3600 --memory=512
command=php /var/www/job2you/artisan queue:work database --queue=sms --sleep=3 --tries=3 --max-time=3600 --memory=128
```
⚠️ Remplacez `/var/www/job2you` par le **vrai chemin** de votre projet. Le worker `sms` est nécessaire pour les alertes SMS (`SendSmsJob`).

### 4. Démarrer
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```
**Attendu :**
```
laravel-queue-scraping:laravel-queue-scraping_00 RUNNING pid 12345, uptime 0:00:01
laravel-queue-sms:laravel-queue-sms_00 RUNNING pid 12346, uptime 0:00:01
```

---

## 🕐 Cron Scheduler (Laravel Schedule)

Ajoutez au crontab de `www-data` (pas root) :
```bash
sudo crontab -u www-data -e
```

```cron
* * * * * cd /var/www/job2you && php artisan schedule:run >> /dev/null 2>&1
```
Cela lance : `ScrapeAllSourcesJob` (toutes les 2 heures) + nettoyage asynchrone (quotidien à 3h).

---

## 🔧 Commandes utiles

| Action | Commande |
|--------|----------|
| Voir statut | `sudo supervisorctl status` |
| Redémarrer workers | `sudo supervisorctl restart laravel-queue-*` |
| Logs scraping | `tail -f /var/www/job2you/storage/logs/worker-scraping.log` |
| Logs SMS | `tail -f /var/www/job2you/storage/logs/worker-sms.log` |
| Vider queue bloquée | `cd /var/www/job2you && php artisan queue:flush` |
| Test manuel | `php artisan tinker --execute='App\Jobs\ScrapeSourceJob::dispatch(22);'` |
| Test SMS direct | `php artisan sms:send +261340000000 "Test Job2You"` |

---

## 🌐 Alternative sans Supervisor (Hébergement mutualisé)

Si **pas d'accès root** (pas de supervisor) :

### Option A : Cron externe + API synchrone
Modifiez `routes/api.php` pour exécuter **synchrone** :
```php
Route::post('/cron/scrape-all', function (Request $request) {
    // ... validation token ...
    $scrapingService = app(\App\Services\Scraping\ScrapingService::class);
    $results = $scrapingService->scrapeAllActive();
    return response()->json(['status' => 'completed', 'results' => $results]);
});
```
Puis cron-job.org appelle `POST /api/cron/scrape-all` toutes les heures.

### Option B : Cron externe + Bouton admin synchrone
Le bouton admin "Lancer la collecte" exécute déjà en synchrone (si `SourceController::scrape()` modifié).
Cron-job.org appelle une route qui dispatch, mais il faut un worker → **pas recommandé sans supervisor**.

---

## 📋 Checklist Activation

```
[ ] Supervisor installé
[ ] laravel-worker.conf copié et chemin adapté
[ ] supervisorctl reread && update
[ ] Cron www-data ajouté (schedule:run)
[ ] Test : clic "Lancer collecte" admin → offres créées
[ ] Test : cron-job.org → logs /admin/logs montrent collecte horaire
```

---

## 🚨 Dépannage

| Symptôme | Cause | Solution |
|----------|-------|----------|
| Jobs restent "pending" | Worker pas démarré | `sudo supervisorctl start laravel-queue-scraping` |
| Erreur "max attempts" | Timeout scraping | Augmenter `--max-time=3600` et `--memory=512` |
| Doublons non gérés | Déduplication OK | Normal si offres déjà en base |
| Pas de logs | Permissions storage | `chmod -R 775 storage && chown -R www-data:www-data storage` |

---

## 📞 Support
- Logs Laravel : `storage/logs/laravel.log`
- Logs activité : `storage/logs/activity-*.log` (visibles dans `/admin/logs`)
- Logs worker : `storage/logs/worker-scraping.log`

---

*Généré automatiquement - Job2You*