#!/bin/sh
# Container start (Render): listen on $PORT, cache config/routes/views with the
# runtime environment (APP_KEY etc. come from Render), then run Apache.
set -e

# Your .env, uploaded in Render as a Secret File (never committed: the repo is public).
# Laravel reads it; real environment variables still win over it — so the image's
# production defaults (APP_ENV, APP_DEBUG=false, sessions…) and anything set in
# Render's Environment tab override a local .env's development values.
if [ -f /etc/secrets/.env ]; then
    cp /etc/secrets/.env /var/www/html/.env
    chown www-data:www-data /var/www/html/.env
fi

sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

if [ -z "$APP_KEY" ] && ! grep -qE '^APP_KEY=.+' /var/www/html/.env 2>/dev/null; then
    echo "APP_KEY is not set — upload your .env as a Secret File or add APP_KEY in Render's Environment." >&2
    exit 1
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
