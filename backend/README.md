# Backend - API Hôtels Laravel

API REST pour la gestion d'hôtels et leurs photos.

## 🛠️ Technologies

- **Laravel 12** - Framework PHP
- **MySQL 8.0** - Base de données
- **PHP 8.3** - Langage
- **PHPUnit** - Framework de tests
- **Docker** - Conteneurisation

## 📁 Structure
```
backend/
├── app/
│   ├── Exceptions/
│   │   └── Handler.php              # Gestion des erreurs API
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── HotelController.php
│   │   │   └── HotelPictureController.php
│   │   ├── Middleware/
│   │   │   └── ForceJsonResponse.php
│   │   └── Requests/
│   │       └── HotelRequest.php     # Validation
│   ├── Models/
│   │   ├── Hotel.php                # Avec suppression fichiers
│   │   └── HotelPicture.php
│   └── Traits/
│       └── ApiResponse.php          # Réponses standardisées
├── config/
│   └── cors.php                     # Configuration CORS
├── database/
│   ├── factories/
│   │   └── HotelFactory.php        # Factory pour tests
│   ├── migrations/
│   │   ├── 2024_11_06_000001_create_hotels_table.php
│   │   └── 2024_11_06_000002_create_hotel_pictures_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── HotelSeeder.php          # 10 hôtels de test
├── routes/
│   └── api.php                      # Routes API
├── tests/
│   ├── Feature/
│   │   ├── HotelTest.php           # 13 tests
│   │   └── HotelPictureTest.php    # 12 tests
│   └── Unit/
└── storage/app/public/hotels/      # Photos uploadées
```

## 🚀 Installation

### Prérequis
- Docker & Docker Compose

### Étapes
```bash
# Depuis la racine du projet
docker-compose up -d --build

# Attendre que MySQL soit prêt (30-60 secondes)
docker-compose logs -f mysql
# Attendre le message "ready for connections"

# Entrer dans le conteneur
docker-compose exec laravel bash

# Installer les dépendances
composer install

# Configuration
php artisan key:generate

# Créer le lien symbolique pour le storage
php artisan storage:link

# Configurer les permissions pour l'upload de fichiers
chmod -R 775 storage
chmod -R 775 public/storage

# Migrations et seeders
php artisan migrate
php artisan db:seed

exit
```

**L'API est maintenant accessible sur : http://localhost:8000**

## 📡 Endpoints API

### Hôtels

| Méthode | Endpoint | Description | Code |
|---------|----------|-------------|------|
| GET | `/api/hotels` | Liste paginée avec filtres | 200 |
| GET | `/api/hotels/search?q={query}` | Recherche | 200 |
| GET | `/api/hotels/{id}` | Détails | 200 |
| POST | `/api/hotels` | Créer | 201 |
| PUT/PATCH | `/api/hotels/{id}` | Modifier | 200 |
| DELETE | `/api/hotels/{id}` | Supprimer | 200 |

### Photos

| Méthode | Endpoint | Description | Code |
|---------|----------|-------------|------|
| POST | `/api/hotels/{id}/pictures` | Upload (multipart) | 201 |
| PATCH | `/api/hotels/{id}/pictures/{pictureId}` | Modifier position | 200 |
| DELETE | `/api/hotels/{id}/pictures/{pictureId}` | Supprimer | 200 |

## 🔍 Exemples de Requêtes

### Lister avec filtres
```bash
GET /api/hotels?city=Paris&min_price=100&max_price=300&per_page=10&sort_by=price_per_night&sort_order=asc
```

**Filtres disponibles :**
- `name`, `city`, `country` : Recherche textuelle
- `min_price`, `max_price` : Filtrage par prix
- `min_capacity` : Capacité minimale
- `sort_by` : name, city, price_per_night, max_capacity, created_at
- `sort_order` : asc, desc
- `per_page` : Pagination (max 100)

### Créer un hôtel
```bash
POST /api/hotels
Content-Type: application/json

{
  "name": "Mon Hôtel",
  "address_1": "123 Rue Example",
  "address_2": null,
  "zip_code": "75001",
  "city": "Paris",
  "country": "France",
  "longitude": 2.3522,
  "latitude": 48.8566,
  "description": "Description...",
  "max_capacity": 50,
  "price_per_night": 150.00
}
```

### Upload de photos
```bash
POST /api/hotels/1/pictures
Content-Type: multipart/form-data

pictures[]: fichier1.jpg
pictures[]: fichier2.jpg
```

**Contraintes :**
- Formats : jpeg, jpg, png, webp
- Taille max : 5 Mo par image
- Upload multiple : oui

## 📊 Réponses JSON

Toutes les réponses suivent une structure cohérente :

### Succès
```json
{
  "success": true,
  "code": 200,
  "message": "Message descriptif",
  "data": { /* ... */ }
}
```

### Erreur
```json
{
  "success": false,
  "code": 404,
  "message": "Ressource non trouvée"
}
```

### Erreur de validation
```json
{
  "success": false,
  "code": 422,
  "message": "Erreur de validation",
  "errors": {
    "name": ["Le nom de l'hôtel est obligatoire"]
  }
}
```

## 🔒 Règles de Validation

### Hotels
- `name` : requis, string, max 255
- `address_1` : requis, string, max 255
- `address_2` : nullable, string, max 255
- `zip_code` : requis, string, max 20
- `city` : requis, string, max 255
- `country` : requis, string, max 255
- `longitude` : requis, numérique, -180 à 180
- `latitude` : requis, numérique, -90 à 90
- `description` : nullable, string, max 5000
- `max_capacity` : requis, entier, 1 à 200
- `price_per_night` : requis, numérique, min 0

## 📸 Gestion des Photos

**Stockage :** `storage/app/public/hotels/{hotel_id}/`

**URL d'accès :** `http://localhost:8000/storage/hotels/{hotel_id}/photo.jpg`

**Fonctionnalités :**
- ✅ Upload multiple
- ✅ Gestion des positions (ordre d'affichage)
- ✅ Suppression en cascade (hôtel → photos BDD + fichiers)
- ✅ Suppression automatique des fichiers physiques
- ✅ Nettoyage des dossiers vides

**Détails de suppression :**
- Supprimer un hôtel → supprime toutes ses photos (BDD + fichiers)
- Supprimer une photo → supprime l'enregistrement BDD + le fichier physique
- Les dossiers vides sont automatiquement nettoyés

## 🧪 Tests

### Lancer les Tests
```bash
docker-compose exec laravel bash

# Tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter HotelTest
php artisan test --filter HotelPictureTest

# Avec verbosité
php artisan test --verbose

exit
```

### Couverture des Tests

**28 tests - 96 assertions**

#### HotelTest (13 tests)
- ✅ Lister les hôtels (vide et avec données)
- ✅ Créer un hôtel
- ✅ Afficher les détails d'un hôtel
- ✅ Mettre à jour un hôtel
- ✅ Supprimer un hôtel
- ✅ Rechercher des hôtels
- ✅ Filtrer par ville et prix
- ✅ Trier par prix
- ✅ Validation (longitude, capacité)

#### HotelPictureTest (12 tests)
- ✅ Upload simple et multiple
- ✅ Validation (format, taille, requis)
- ✅ Incrémentation des positions
- ✅ Mettre à jour la position
- ✅ Supprimer une photo
- ✅ Sécurité (403 pour photos d'autres hôtels)
- ✅ Suppression en cascade

### Ce que les Tests Vérifient

**Structure des réponses :**
- ✅ Champs `success`, `code`, `message` présents
- ✅ Codes HTTP corrects (200, 201, 404, 422)
- ✅ Structure de pagination cohérente

**Validation :**
- ✅ Champs requis
- ✅ Limites de valeurs
- ✅ Types de fichiers
- ✅ Taille des fichiers (5MB max)

**Intégrité :**
- ✅ Données insérées correctement
- ✅ Mises à jour fonctionnelles
- ✅ Suppression en cascade (BDD + fichiers)

**Sécurité :**
- ✅ Impossible de modifier les ressources d'un autre hôtel

## 🗄️ Base de Données

### Table `hotels`
- id, name, address_1, address_2, zip_code
- city, country, longitude, latitude
- description, max_capacity, price_per_night
- created_at, updated_at

### Table `hotel_pictures`
- id, hotel_id (FK cascade), filepath
- filesize, position
- created_at, updated_at

**Suppression en cascade :** 
- Supprimer un hôtel supprime automatiquement ses photos en BDD
- Le modèle Hotel utilise un événement `deleting` pour supprimer les fichiers physiques

## 🌐 CORS

Autorise les requêtes depuis :
- `http://localhost:3000` (Next.js)
- `http://127.0.0.1:3000`

Configuration : `config/cors.php`

## 🛠️ Commandes Artisan Utiles
```bash
# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Storage
php artisan storage:link

# Permissions
chmod -R 775 storage
chmod -R 775 public/storage

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Routes
php artisan route:list
```

## 📝 Notes Importantes

- **Seeder** : 10 hôtels de test sans photos
- **Upload** : Via API ou frontend uniquement
- **Cascade** : Suppression hôtel → supprime photos (BDD + fichiers physiques)
- **Messages** : Tous en français
- **Tests** : Base de données en mémoire pour rapidité
- **Permissions** : Les permissions storage sont critiques pour l'upload

## 🚨 Troubleshooting

### Erreur de connexion MySQL
```bash
# Attendre l'initialisation (30-60s)
docker-compose logs mysql
```

### Erreur storage link
```bash
php artisan storage:link
```

### Erreur 403 sur les images
```bash
chmod -R 775 storage
chmod -R 775 public/storage
```

### Photos non supprimées
```bash
# Vérifier les permissions
ls -la storage/app/public/hotels/
chmod -R 775 storage/app/public/hotels/
```

### Réinitialiser complètement
```bash
docker-compose down -v
docker-compose up -d --build
# Puis refaire l'installation complète
```

## 📄 Licence

Test technique.