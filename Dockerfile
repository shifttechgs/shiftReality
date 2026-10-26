# Production image for Render (Docker runtime).
# 1. PHP dependencies  2. front-end build (Vite)  3. Apache + PHP serving /public on $PORT

# ── 1. Composer ───────────────────────────────────────────────────────────
FROM php:8.4-cli-alpine AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# ── 2. Front-end assets ───────────────────────────────────────────────────
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY --from=vendor /app /app
RUN npm run build

# ── 3. Runtime ────────────────────────────────────────────────────────────
FROM php:8.4-apache

RUN a2enmod rewrite headers expires \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && docker-php-ext-install opcache

WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build
COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start \
    && chown -R www-data:www-data storage bootstrap/cache

# Defaults for a stateless container; anything set in Render's dashboard wins.
# Sessions live in cookies and the cache on disk, so no database is needed.
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    SESSION_DRIVER=cookie \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    PORT=10000

EXPOSE 10000
CMD ["start"]
