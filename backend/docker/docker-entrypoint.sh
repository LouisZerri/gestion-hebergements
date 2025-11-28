#!/bin/bash
set -e

# Créer les dossiers et fixer les permissions
mkdir -p /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Garder le container actif
exec tail -f /dev/null