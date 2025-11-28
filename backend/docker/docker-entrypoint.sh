#!/bin/bash
set -e

# Créer les dossiers et fixer les permissions
mkdir -p /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copier .env si absent
if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

# Garder le container actif
exec tail -f /dev/null