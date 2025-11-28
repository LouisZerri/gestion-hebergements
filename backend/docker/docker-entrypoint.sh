#!/bin/bash
set -e

# Créer les dossiers storage complets et fixer les permissions
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copier .env si absent
if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

# Lancer le serveur Laravel
exec php artisan serve --host=0.0.0.0 --port=8000