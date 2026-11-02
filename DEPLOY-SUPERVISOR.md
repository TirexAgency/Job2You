# Installation Supervisor - Job2You

## 1. Sur le serveur (via SSH)

```bash
# Installer supervisor
sudo apt update && sudo apt install -y supervisor

# Copier la config (adapter le chemin si différent de /var/www/job2you)
sudo cp /var/www/job2you/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Vérifier le chemin dans le fichier
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
# ⚠️ Modifier /var/www/job2you → votre vrai chemin si différent

# Recharger et démarrer
sudo supervisorctl reread
sudo supervisorctl update

# Vérifier
sudo supervisorctl status
```

## 2. Cron pour le Scheduler (Laravel Schedule)

```bash
# En tant que www-data
sudo crontab -u www-data -e
```

Ajouter :
```cron
* * * * * cd /var/www/job2you && php artisan schedule:run >> /dev/null 2>&1
```

## 3. Après chaque déploiement

```bash
# Redémarrer les workers pour prendre en compte le nouveau code
sudo supervisorctl restart laravel-queue-*
```

## 4. Commandes utiles

```bash
# Statut
sudo supervisorctl status

# Logs temps réel
sudo tail -f /var/www/job2you/storage/logs/worker-scraping.log

# Redémarrer seulement scraping
sudo supervisorctl restart laravel-queue-scraping

# Arrêter (maintenance)
sudo supervisorctl stop laravel-queue-*
```

## 5. Que font les workers ?

| Worker | Queue | Rôle |
|--------|-------|------|
| `laravel-queue-default` (x2) | `default` | Emails, notifications, jobs divers |
| `laravel-queue-scraping` (x1) | `scraping` | **Scraping sources** (ScrapeSourceJob) |
| `laravel-queue-emails` (x1) | `emails` | Envoi emails (si queue dédiée) |

## 6. Vérifier que ça marche

```bash
# Test manuel scraping
cd /var/www/job2you
php artisan tinker --execute='
$source = App\Models\Source::where("active", true)->first();
App\Jobs\ScrapeSourceJob::dispatch($source->id);
echo "Job dispatché pour: " . $source->name;
'

# Regarder le log worker
tail -f storage/logs/worker-scraping.log

# Dans l'admin : /admin/logs
# Doit afficher "🔄 Collecte démarrée" puis "✅ Collecte terminée"
```