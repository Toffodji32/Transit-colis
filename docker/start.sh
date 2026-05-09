#!/bin/sh
echo "▶ Correction des permissions..."
chmod -R 777 /var/www/html/var/

echo "▶ Lancement des migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "▶ Nettoyage du cache..."
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

echo "▶ Démarrage PHP-FPM..."
php-fpm -D

echo "▶ Démarrage Nginx..."
nginx -g "daemon off;"