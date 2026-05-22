# BlogHub - Frontend Nuxt 3

## ⚡ Installation rapide

### 1. Copiez tous ces fichiers dans votre projet Nuxt 3 existant
```
Votre projet Nuxt 3/
├── app.vue                ← remplacer
├── nuxt.config.ts         ← remplacer
├── package.json           ← remplacer
├── tailwind.config.ts     ← remplacer
├── tsconfig.json          ← remplacer
├── .env.example           ← copier
├── assets/css/main.css    ← créer le dossier
├── components/            ← copier tout
├── composables/           ← copier tout
├── layouts/               ← copier tout
├── middleware/            ← copier tout
├── pages/                 ← copier tout
├── plugins/               ← copier tout
└── stores/                ← copier tout
```

### 2. Configurer l'URL de l'API
Créez un fichier `.env` à la racine :
```env
NUXT_PUBLIC_API_BASE=http://mon-blog.test/api
```

### 3. Installer les dépendances
```bash
npm install
```

### 4. Lancer
```bash
npm run dev
```

Accédez à **http://localhost:3000** → vous serez redirigé vers `/auth/login`
