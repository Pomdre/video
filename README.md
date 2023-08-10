# Video hover prosjekt
## Cron jobb
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
## For å starte den lokalt
```
php artisan schedule:run
eller
php artisan schedule:work
```

## Installer
```
composer install --optimize-autoloader --no-dev
php artisan key:generate
npm install
php artisan migrate --force
chgrp -R www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
php artisan storage:link
ln -s video_bbase_dir video/public/storage/video
```
## Tips
Dersom det oppstår problemer prøv:
```
php artisan optimize
```
Husk å lag bruker! Filen finnes under:
```
config/fortify.php

Dersom du ikke ønsker at hvemsom helst kan registrere seg
//Features::registration()
```

# Lenker
```
/register
/login
/dashboard
```