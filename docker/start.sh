#!/bin/sh
# Container start (Render): listen on $PORT, cache config/routes/views with the
# runtime environment (APP_KEY etc. come from Render), then run Apache.
set -e

sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set — add it in Render's environment settings." >&2
    exit 1
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
