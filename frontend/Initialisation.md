# BlogHub Frontend - Nuxt 3

Une plateforme de blog moderne et intuitive construite avec Nuxt 3, Vue 3 et TailwindCSS.

## 🚀 Fonctionnalités

### Authentification
- ✅ Inscription et connexion utilisateur
- ✅ Gestion de session avec tokens
- ✅ Rôles admin/utilisateur

### Gestion des Articles
- ✅ Créer, lire, modifier, supprimer des articles
- ✅ Statuts draft/published
- ✅ Image de couverture
- ✅ Upload d'images additionnelles
- ✅ Pagination

### Commentaires
- ✅ Ajouter des commentaires
- ✅ Modifier ses propres commentaires
- ✅ Supprimer les commentaires
- ✅ Affichage en temps réel

### Newsletter
- ✅ Formulaire d'abonnement
- ✅ Confirmation par email
- ✅ Gestion des abonnés (admin)

### Panneau d'Administration
- ✅ Gestion des utilisateurs
- ✅ Création de comptes admin/user
- ✅ Liste des abonnés newsletter
- ✅ Statistiques globales

## 📋 Prérequis

- Node.js 16+ et npm/yarn
- Un backend Laravel fonctionnel (voir documentation API)

## 🔧 Installation

### 1. Cloner le projet
```bash
git clone <repository-url>
cd bloghub-frontend
```

### 2. Installer les dépendances
```bash
npm install
# ou
yarn install
```

### 3. Configuration
Créer un fichier `.env.local` à partir de `.env.example` :
```bash
cp .env.example .env.local
```

Adapter l'URL de l'API si nécessaire :
```env
NUXT_PUBLIC_API_BASE=http://votre-api.local/api
```

### 4. Lancer le serveur de développement
```bash
npm run dev
# ou
yarn dev
```

L'application sera accessible à `http://localhost:3000`

## 📁 Structure du projet

```
├── assets/
│   └── css/
│       └── main.css           # Styles globaux
├── components/
│   ├── ArticleCard.vue        # Carte article
│   ├── CommentItem.vue        # Commentaire
│   ├── NewsletterForm.vue     # Formulaire newsletter
│   └── NotificationContainer.vue # Notifications
├── composables/
│   ├── useApi.ts             # Utilitaires API
│   └── useNotification.ts    # Système de notifications
├── layouts/
│   ├── default.vue           # Layout principal
│   └── auth.vue              # Layout authentification
├── middleware/
│   └── auth.ts               # Protection des routes
├── pages/
│   ├── index.vue             # Accueil
│   ├── auth/
│   │   ├── login.vue         # Connexion
│   │   └── register.vue      # Inscription
│   ├── articles/
│   │   ├── index.vue         # Liste articles
│   │   ├── create.vue        # Créer article
│   │   ├── mine.vue          # Mes articles
│   │   ├── [id].vue          # Détail article
│   │   └── [id]/
│   │       └── edit.vue      # Éditer article
│   ├── admin/
│   │   ├── index.vue         # Dashboard admin
│   │   ├── users.vue         # Gestion utilisateurs
│   │   └── subscribers.vue   # Abonnés newsletter
│   └── profile.vue           # Profil utilisateur
├── stores/
│   ├── authStore.ts          # Authentification
│   ├── articleStore.ts       # Articles
│   ├── commentStore.ts       # Commentaires
│   ├── userStore.ts          # Utilisateurs
│   └── newsletterStore.ts    # Newsletter
├── nuxt.config.ts            # Configuration Nuxt
├── tailwind.config.ts        # Configuration Tailwind
└── package.json              # Dépendances

```

## 🔐 Authentification

### Login/Register
Les utilisateurs peuvent s'inscrire ou se connecter via les pages dédiées.
Un token JWT est généré et stocké dans le localStorage.

### Rôles
- **Utilisateur normal** : Peut créer/modifier/supprimer ses propres articles et commentaires
- **Administrateur** : Accès complet au panneau d'administration

## 🎨 Styling

Le projet utilise TailwindCSS avec une configuration personnalisée.

### Couleurs principales
- Primary: Indigo (#6366f1)
- Secondary: Gris (#1f2937)
- Accent: Rose (#ec4899)

### Typographie
- Display: Playfair Display (titres)
- Body: Inter (texte)

## 📡 API

Toutes les requêtes API incluent automatiquement le token d'authentification.

### Points d'accès principaux
- `POST /register` - Inscription
- `POST /login` - Connexion
- `POST /logout` - Déconnexion
- `GET /articles` - Lister les articles
- `POST /articles` - Créer un article
- `GET /articles/{id}` - Détail d'un article
- `PUT /articles/{id}` - Modifier un article
- `DELETE /articles/{id}` - Supprimer un article
- `POST /articles/{id}/comments` - Ajouter un commentaire
- `PUT /comments/{id}` - Modifier un commentaire
- `DELETE /comments/{id}` - Supprimer un commentaire
- `POST /articles/{id}/images` - Upload d'image
- `DELETE /article-images/{id}` - Supprimer une image
- `POST /newsletter/subscribe` - S'abonner
- `GET /users` - Lister les utilisateurs (admin)
- `POST /users` - Créer un utilisateur (admin)
- `DELETE /users/{id}` - Supprimer un utilisateur (admin)

## 🚀 Déploiement

### Build de production
```bash
npm run build
```

### Prévisualiser le build
```bash
npm run preview
```

### Générer les pages statiques
```bash
npm run generate
```

## 🛠️ Technologies utilisées

- **Framework**: Nuxt 3
- **Langage UI**: Vue 3
- **Styling**: TailwindCSS
- **Gestion d'état**: Pinia
- **Communication API**: Native fetch
- **Autentification**: Bearer tokens (JWT)

## 📝 Notes importantes

### Stockage local
- Les données d'authentification sont stockées dans le localStorage
- Supprimer le token supprime l'accès

### Permissions
- Les articles brouillon ne sont visibles que par leur auteur/admin
- Seul le propriétaire/admin peut éditer/supprimer
- Les admins ont accès au panneau de gestion

### Images
- Taille maximale: 2 Mo
- Formats supportés: JPEG, PNG, GIF, WebP
- Chemin de stockage: `/storage/`

## 🐛 Dépannage

### Erreur de connexion à l'API
Vérifier l'URL dans `.env.local` et que le backend est accessible.

### Token expiré
L'application redirige automatiquement vers le login si le token est invalide.

### Images non chargées
S'assurer que le backend expose correctement le chemin `/storage/public/`.

## 📧 Support

Pour toute question ou problème, consulter la documentation du backend ou contacter l'équipe de développement.

## 📄 Licence

Ce projet est fourni tel quel à des fins d'évaluation.
