# Gestion d'Hébergements - Application Full Stack

Application de gestion d'hôtels avec API REST Laravel et interface Next.js.

## 🛠️ Stack Technique

### Backend
- **Laravel 12** - API REST
- **MySQL 8.0** - Base de données
- **PHP 8.3** - Langage
- **PHPUnit** - Tests

### Frontend
- **Next.js 15** - Framework React
- **React 19** - Bibliothèque UI
- **Chakra UI** - Design system
- **TypeScript** - Typage statique

### Infrastructure
- **Docker** - Conteneurisation
- **Docker Compose** - Orchestration

## 📋 Prérequis

- Docker
- Docker Compose
- Git

## 🚀 Installation Rapide
```bash
# 1. Cloner le projet
git clone https://github.com/LouisZerri/gestion-hebergements.git
cd gestion-hebergements

# 2. Démarrer les conteneurs
docker-compose up -d --build

# 3. Attendre 30 secondes que MySQL démarre

# 4. Configurer Laravel
docker-compose exec laravel composer install
docker-compose exec laravel php artisan key:generate
docker-compose exec laravel php artisan storage:link

# 5. Configurer les variables d'environnement
Modifier le fichier backend/.env avec les informations suivantes :

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password

# 6. Lancer les migrations et seeds
docker-compose exec laravel php artisan migrate --seed

# 7. Accéder à l'application
Frontend : http://localhost:3000
Backend  : http://localhost:8000
```

## 📁 Structure du Projet
```
.
├── docker-compose.yml          # Orchestration des services
├── backend/                    # API Laravel
│   ├── app/
│   ├── database/
│   ├── routes/
│   ├── tests/
│   └── README.md              # Documentation backend détaillée
├── frontend/                   # Application Next.js
│   ├── app/
│   ├── components/
│   └── README.md              # Documentation frontend détaillée
└── README.md                  # Ce fichier
```

## 🔗 Accès aux Services

| Service | URL | Description |
|---------|-----|-------------|
| Frontend | http://localhost:3000 | Interface utilisateur Next.js |
| Backend API | http://localhost:8000/api | API REST Laravel |
| MySQL | localhost:3306 | Base de données |

## 📚 Documentation Détaillée

- **[Documentation Backend](backend/README.md)** - API, endpoints, tests
- **[Documentation Frontend](frontend/README.md)** - Architecture, composants

## 🧪 Lancer les Tests
```bash
# Tests backend
docker-compose exec laravel php artisan test

```

## 📄 Licence

Projet développé dans le cadre d'un test technique.
