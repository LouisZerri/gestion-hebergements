# Frontend - Application Next.js de Gestion d'Hôtels

Interface web pour gérer les hôtels et leurs photos.

## 🛠️ Technologies

- **Next.js 15** - Framework React
- **React 19** - Bibliothèque UI
- **TypeScript** - Typage statique
- **Chakra UI v3** - Composants UI
- **React Hot Toast** - Notifications
- **Docker** - Conteneurisation

## 📁 Structure
```
frontend/
├── app/
│   ├── fonts/                    # Polices
│   ├── hotels/
│   │   ├── [id]/
│   │   │   ├── edit/
│   │   │   │   └── page.tsx     # Modification hôtel + photos
│   │   │   └── page.tsx         # Détails hôtel
│   │   └── new/
│   │       └── page.tsx         # Création hôtel + photos
│   ├── layout.tsx               # Layout principal
│   ├── page.tsx                 # Page d'accueil (liste)
│   └── providers.tsx            # Providers Chakra UI
├── components/
│   └── PhotoManager.tsx         # Composant gestion photos
├── lib/
│   ├── api.ts                   # Service API
│   └── toast.ts                 # Utilitaire toast
├── types/
│   └── api.ts                   # Types TypeScript
└── public/                      # Assets statiques
```

## 🚀 Installation

### Prérequis
- Docker & Docker Compose
- Backend Laravel lancé sur http://localhost:8000

### Étapes
```bash
# Depuis la racine du projet
docker-compose up -d --build

# Attendre que Next.js démarre (30 secondes)

# IMPORTANT : Installer les dépendances dans le conteneur
docker-compose exec nextjs sh
npm install
exit

# Redémarrer Next.js
docker-compose restart nextjs

# Attendre 10-20 secondes
```

**L'application est maintenant accessible sur : http://localhost:3000**

## 🎯 Fonctionnalités

### Page d'accueil (/)
- ✅ Liste paginée des hôtels (5 par page)
- ✅ Recherche par nom ou ville
- ✅ Affichage : photo, nom, description, ville, capacité, prix
- ✅ Pagination complète
- ✅ Bouton "+ Nouvel Hôtel"

### Page de détails (/hotels/[id])
- ✅ Galerie photos avec miniatures cliquables
- ✅ Informations complètes de l'hôtel
- ✅ Boutons "Modifier" et "Supprimer"
- ✅ Confirmation avant suppression
- ✅ Affichage des métadonnées (créé le, modifié le)

### Page de création (/hotels/new)
- ✅ Formulaire complet avec validation
- ✅ Tous les champs obligatoires indiqués
- ✅ Messages d'erreur en temps réel
- ✅ Upload de photos après création
- ✅ Validation côté client et serveur

### Page de modification (/hotels/[id]/edit)
- ✅ Formulaire pré-rempli
- ✅ Modification des informations
- ✅ Gestion complète des photos :
  - Upload multiple
  - Réorganisation (↑ ↓)
  - Suppression
  - Prévisualisation

## 🖼️ Gestion des Photos

Le composant `PhotoManager` permet :

**Upload :**
- Sélection multiple de fichiers
- Validation (format, taille)
- Preview avant upload
- Upload en un clic

**Réorganisation :**
- Boutons ↑ ↓ sur chaque photo
- Modification de la position
- Mise à jour en temps réel

**Suppression :**
- Bouton × sur chaque photo
- Suppression immédiate (BDD + fichier)

**Contraintes :**
- Formats acceptés : JPEG, JPG, PNG, WEBP
- Taille max : 5 Mo par image
- Upload uniquement après création de l'hôtel

## 📡 Communication API

### Service API (`lib/api.ts`)

**Méthodes disponibles :**
```typescript
// Hôtels
apiService.getHotels(filters?)      // Liste avec filtres
apiService.searchHotels(query)      // Recherche
apiService.getHotel(id)             // Détails
apiService.createHotel(data)        // Créer
apiService.updateHotel(id, data)    // Modifier
apiService.deleteHotel(id)          // Supprimer

// Photos
apiService.uploadPictures(hotelId, files)                    // Upload
apiService.updatePicturePosition(hotelId, pictureId, pos)    // Position
apiService.deletePicture(hotelId, pictureId)                 // Supprimer
apiService.getImageUrl(filepath)                             // URL image
```

**Gestion d'erreurs :**
- Erreurs réseau
- Erreurs 404 (ressource non trouvée)
- Erreurs 422 (validation)
- Messages d'erreur formatés

## 🎨 Design & UX

**Chakra UI v3 :**
- Composants : Box, Button, Container, Table, Field, etc.
- Thème : Mode clair par défaut
- Responsive : Mobile, tablet, desktop

**Toast Notifications :**
- Succès (vert) : Opérations réussies
- Erreur (rouge) : Problèmes
- Position : Top-right
- Durée : 4-5 secondes

**États de chargement :**
- Spinners pendant les requêtes
- Boutons disabled pendant les opérations
- Messages de chargement explicites

## 🔧 Configuration

### Variables d'environnement

Créez `.env.local` :
```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

### Types TypeScript

Tous les types sont définis dans `types/api.ts` :
- `Hotel` : Modèle hôtel
- `HotelPicture` : Modèle photo
- `HotelFormData` : Données formulaire
- `ApiResponse<T>` : Réponse API
- `PaginatedResponse<T>` : Réponse paginée

## 🧪 Tests Manuels

### Scénario complet

1. **Liste** : Vérifier affichage 5 hôtels
2. **Recherche** : Tester recherche par ville
3. **Création** :
   - Créer un hôtel
   - Vérifier validation
   - Upload 3 photos
   - Vérifier affichage
4. **Détails** :
   - Cliquer sur "Détails"
   - Vérifier galerie photos
   - Tester navigation miniatures
5. **Modification** :
   - Cliquer sur "Modifier"
   - Changer le prix
   - Upload 2 photos supplémentaires
   - Réorganiser les photos
   - Supprimer une photo
6. **Suppression** :
   - Cliquer sur "Supprimer"
   - Confirmer
   - Vérifier redirection

## 🚨 Troubleshooting

### Page blanche / Module not found
```bash
docker-compose exec nextjs sh
npm install
exit
docker-compose restart nextjs
```

### Images ne s'affichent pas
```bash
# Vérifier les permissions backend
docker-compose exec laravel chmod -R 775 storage
docker-compose exec laravel chmod -R 775 public/storage
docker-compose exec laravel php artisan storage:link
```

### Erreur de connexion API
```bash
# Vérifier que le backend fonctionne
curl http://localhost:8000/api/hotels

# Vérifier la variable d'environnement
docker-compose exec nextjs sh
echo $NEXT_PUBLIC_API_URL
exit
```

### Toast ne s'affiche pas
```bash
# Vérifier que react-hot-toast est installé
docker-compose exec nextjs sh
npm list react-hot-toast
# Si absent :
npm install react-hot-toast
exit
docker-compose restart nextjs
```

### Erreur TypeScript
```bash
# Nettoyer le cache Next.js
docker-compose exec nextjs sh
rm -rf .next
exit
docker-compose restart nextjs
```

## 🔄 Workflow de Développement
```bash
# Lancer en mode dev
docker-compose up -d

# Voir les logs
docker-compose logs -f nextjs

# Modifier du code
# → Hot reload automatique

# Ajouter une dépendance
docker-compose exec nextjs npm install package-name
docker-compose restart nextjs

# Arrêter
docker-compose down
```

## 📝 Notes Importantes

- **npm install** : Obligatoire après `docker-compose up`
- **Backend requis** : L'API doit tourner sur :8000
- **CORS** : Configuré côté backend
- **Permissions** : Storage Laravel doit être accessible
- **Photos** : Upload uniquement après création hôtel

## 📄 Licence

Test technique.