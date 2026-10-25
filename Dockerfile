# Production image for Customyzables.
# Works on Railway, Render, Fly.io, or any VPS with Docker installed.
#
# Assets are built inside the image (stage 1), so the deployment does not depend on
# committed build files or on Node being installed on the server.

# --- Stage 1: build CSS/JS with Vite ---------------------------------------
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Tailwind also scans app/ for class names returned by PHP enums (status badges),
# so it has to be present at build time.
COPY resources ./resources
COPY app ./app
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# --- Stage 2: PHP 8.1 + Apache ---------------------------------------------
FROM php:8.1-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libonig-dev libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring zip intl opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Point Apache at Laravel's public/ directory.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dependency layer first so rebuilds stay fast when only app code changes.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Application code + compiled assets from stage 1.
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/app/public storage/framework/sessions storage/framework/views \
                storage/framework/cache storage/logs bootstrap/cache \
    && php artisan storage:link \
    && chown -R www-data:www-data storage bootstrap/cache

# Entrypoint: migrate, cache, then start Apache.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint && chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]
CMD ["apache2-foreground"]

EXPOSE 80
