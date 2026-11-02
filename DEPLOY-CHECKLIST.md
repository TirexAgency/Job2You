# Checklist Déploiement Production - Job2You

## 📁 Fichiers à uploader via FileZilla (5 fichiers)

| # | Fichier local | Destination serveur | Type |
|---|---------------|---------------------|------|
| 1 | `routes/api.php` | `/var/www/job2you/routes/api.php` | **NOUVEAU** |
| 2 | `bootstrap/app.php` | `/var/www/job2you/bootstrap/app.php` | **MODIFIÉ** |
| 3 | `config/app.php` | `/var/www/job2you/config/app.php` | **MODIFIÉ** |
| 4 | `.env.prod` → `.env` | `/var/www/job2you/.env` | **MODIFIÉ + RENOMMÉ** |
| 5 | `laravel-worker.conf` | `/var/www/job2you/laravel-worker.conf` | **NOUVEAU** (pour ref) |

---

## ⚠️ Actions AVANT upload

### 1. Générer VRAI CRON_TOKEN
```bash
# Terminal LOCAL
php -r "echo bin2hex(random_bytes(32)).PHP_EOL;"
# Exemple: a1b2c3d4e5f678901234567890abcdef1234567890abcdef1234567890abcdef
```

### 2. Modifier `.env.prod` avec ce token
```env
CRON_TOKEN=a1b2c3d4e5f678901234567890abcdef1234567890abcdef1234567890abcdef
```

### 3. Upload `.env.prod` → renommer en `.env` sur serveur

---

## 🔧 Après upload (si accès SSH)

```bash
cd /var/www/job2you

# 1. Vider caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 3. Supervisor (voir DEPLOY-QUEUE-WORKER.md)
sudo cp laravel-worker.conf /etc/supervisor/conf.d/
sudo nano /etc/supervisor/conf.d/laravel-worker.conf  # Adapter chemin
sudo supervisorctl reread && sudo supervisorctl update

# 4. Cron scheduler
sudo crontab -u www-data -e
# * * * * * cd /var/www/job2you && php artisan schedule:run >> /dev/null 2>&1

# 5. Test
curl -X POST https://job2you.live-preprod.site/api/cron/scrape-all \
  -H "X-Cron-Token: VOTRE_TOKEN" -H "Content-Type: application/json"
# {"status":"dispatched"}
```

---

## 🌐 Config cron-job.org (gratuit)

| Paramètre | Valeur |
|-----------|--------|
| **URL** | `https://job2you.live-preprod.site/api/cron/scrape-all` |
| **Méthode** | `POST` |
| **Headers** | `X-Cron-Token: VOTRE_TOKEN`<br>`Content-Type: application/json` |
| **Schedule** | `Every hour` (`0 * * * *`) |

---

## ✅ Vérifications post-déploiement

| Test | Résultat attendu |
|------|------------------|
| `GET /admin/sources` | 2 sources actives (Asako, Tanitjobs) |
| Clic ▶️ "Lancer collecte" | "Collecte terminée : X nouvelle(s)" |
| `GET /admin/logs` | Logs "🔄 Collecte démarrée" / "✅ Collecte terminée" |
| Cron-job.org (1h plus tard) | Nouvelles offres si sources ont publié |

---

## 📚 Documentation créée

| Fichier | Usage |
|---------|-------|
| `DEPLOY-QUEUE-WORKER.md` | Config complète Supervisor + Cron |
| `DEPLOY-SUPERVISOR.md` | Quick-start Supervisor |
| `laravel-worker.conf` | Config Supervisor prête à l'emploi |

---

## 🚨 Points critiques

1. **CRON_TOKEN** : Doit être **identique** dans `.env` (serveur) et cron-job.org
2. **Chemin projet** : Adapter `/var/www/job2you` dans `laravel-worker.conf` si différent
3. **User www-data** : Doit être le user PHP-FPM/Nginx
3. **Queue worker** : Sans Supervisor → scraping asynchrone **ne marchera pas**

---

*Checklist générée - Job2You*