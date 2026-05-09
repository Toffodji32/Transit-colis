FROM php:8.2-fpm-alpine

# Dépendances système + extensions PHP
RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    libzip-dev \
    unzip \
    icu-dev \
    && docker-php-ext-install pdo pdo_pgsql zip intl

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier le projet
COPY . .

# Installer les dépendances
RUN APP_ENV=prod COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Permissions sur var/
RUN mkdir -p var && chown -R www-data:www-data var/

# Config nginx et script de démarrage
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080
CMD ["/start.sh"]