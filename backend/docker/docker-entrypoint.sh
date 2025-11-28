#!/bin/bash
set -e

# Créer les dossiers et fixer les permissions
mkdir -p /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copier .env si absent
if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

# Installer les dépendances si vendor n'existe pas
if [ ! -d /var/www/vendor ]; then
    composer install
fi

# Lancer le serveur Laravel
exec php artisan serve --host=0.0.0.0 --port=8000