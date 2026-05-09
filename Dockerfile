FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    libzip-dev \
    unzip \
    icu-dev \
    && docker-php-ext-install pdo pdo_pgsql zip intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN APP_ENV=prod COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

RUN mkdir -p var/cache/prod var/log \
    && APP_ENV=prod php bin/console asset-map:compile \
    && chmod -R 777 /var/www/html/var/ \
    && chown -R www-data:www-data /var/www/html/

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080
CMD ["/start.sh"]