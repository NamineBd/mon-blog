# API Documentation - Blog Laravel

Base URL : `http://mon-blog.test/api`

Toutes les routes protégées nécessitent un token d’authentification fourni dans l’en-tête  
`Authorization: Bearer {token}`.

---

## Authentification

### Inscription
`POST /register`

**Paramètres (form-data ou JSON)**

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| name | string | oui | Nom complet |
| email | string | oui | Email unique |
| password | string | oui | Minimum 8 caractères |
| password_confirmation | string | oui | Confirmation du mot de passe |

**Réponse (201 Created)**
```json
{
  "user": {
    "id": 1,
    "name": "Jean Dupont",
    "email": "jean@example.com",
    "is_admin": false
  },
  "token": "1|abcdef..."
}

Connexion
POST /login

Paramètres (JSON)

{
  "email": "jean@example.com",
  "password": "secret"
}

Réponse (200 OK)

{
  "message": "Connexion réussie",
  "token": "1|abcdef...",
  "user": {
    "id": 1,
    "name": "Jean Dupont",
    "email": "jean@example.com",
    "is_admin": false
  }
}

Déconnexion
POST /logout (protégé)

Réponse (200 OK)

{ "message": "Déconnecté" }

Utilisateur courant
GET /me (protégé)

Réponse (200 OK)

{
  "id": 1,
  "name": "Jean Dupont",
  "email": "jean@example.com",
  "is_admin": false,
  "created_at": "2025-01-01T00:00:00.000000Z",
  "updated_at": "2025-01-01T00:00:00.000000Z"
}

Articles
Lister les articles
GET /articles (protégé)

Admin : voit tous les articles (publiés + brouillons) paginés par 10.

Non‑admin : voit uniquement ses propres articles (tous statuts) + les articles publiés des autres.

Réponse (200 OK)

{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Mon article",
      "content": "...",
      "excerpt": "Résumé",
      "cover_image": "covers/fichier.jpg",
      "status": "published",
      "published_at": "2025-01-01T10:00:00.000000Z",
      "user_id": 1,
      "user": { "id": 1, "name": "Jean Dupont", "email": "jean@example.com" },
      "comments": [],
      "images": []
    }
  ],
  "total": 20
}

Créer un article
POST /articles (protégé)
Content-Type : multipart/form-data

Champ	Type	Requis	Description
title	string	oui	Titre de l’article
content	string	oui	Contenu HTML ou texte
excerpt	string	non	Extrait / description courte
cover_image	file	non	Image (max 2 Mo, formats image)
status	string	oui	draft ou published

Réponse (201 Created)

{
  "id": 1,
  "title": "Nouvel article",
  "content": "...",
  "status": "draft",
  "user_id": 1,
  "cover_image": "covers/xxx.jpg",
  "updated_at": "...",
  "created_at": "..."
}

Voir un article
GET /articles/{id} (protégé)

Accès : article publié OU propriétaire OU admin.

Réponse (200 OK) : (structure d’un article avec relations)

Erreur (403)

{ "message": "Unauthorized" }

Modifier un article
PUT /articles/{id} ou PATCH /articles/{id} (protégé)

Accès : propriétaire ou admin.

Paramètres (multipart/form-data, tous optionnels) : title, content, excerpt, cover_image (fichier), status.

Si status passe de draft à published, published_at est automatiquement positionné à now().

Réponse (200 OK) : l’article mis à jour avec ses relations.

Supprimer un article
DELETE /articles/{id} (protégé)

Supprime l’article, sa cover image et toutes ses images associées.

Réponse (200 OK)

{ "message": "Article deleted" }

Commentaires
Lister les commentaires d’un article
GET /articles/{id}/comments (protégé)

Accès : voir l’article (publié ou propriétaire/admin).

Réponse (200 OK)

[
  {
    "id": 1,
    "content": "Super article !",
    "user_id": 2,
    "article_id": 1,
    "created_at": "...",
    "user": { "id": 2, "name": "Marie" }
  }
]

Ajouter un commentaire
POST /articles/{id}/comments (protégé)

{ "content": "Mon commentaire" }

Réponse (201 Created) : le commentaire avec l’utilisateur.

Modifier un commentaire
PUT /comments/{id} (protégé)

Accès : propriétaire du commentaire ou admin.

Paramètres

{ "content": "Nouveau texte" }

Réponse (200 OK) : commentaire modifié avec l’utilisateur.

Supprimer un commentaire
DELETE /comments/{id} (protégé)

Accès : propriétaire ou admin.

Réponse (200 OK)

{ "message": "Comment deleted" }

Images d’articles
Upload d’une image
POST /articles/{id}/images (protégé)
Content-Type : multipart/form-data

Accès : propriétaire de l’article ou admin.

Champ	Type	Requis	Description
image	file	oui	max 2 Mo, image

{
  "id": 10,
  "article_id": 1,
  "image_path": "articles/xxx.jpg",
  "created_at": "..."
}

Lister les images d’un article
GET /articles/{id}/images (protégé)

Accès : voir l’article (publié ou propriétaire/admin).

Réponse (200 OK)

[
  { "id": 10, "article_id": 1, "image_path": "articles/xxx.jpg" }
]

Supprimer une image
DELETE /article-images/{id} (protégé)

Accès : propriétaire de l’article parent ou admin.

Réponse (200 OK)

{ "message": "Image deleted" }

Newsletter
S’abonner (publique)
POST /newsletter/subscribe

Paramètres

{ "email": "user@example.com" }

Réponse (200 OK)

{
  "message": "Please check your email to confirm subscription.",
  "subscriber": { "email": "user@example.com", "created_at": "..." }
}

Un email de confirmation est envoyé.

Confirmer l’abonnement
GET /newsletter/confirm/{id} (publique)

id = l’identifiant du subscriber.
Met verified_at à la date courante.

Réponse (200 OK)

{ "message": "Subscription confirmed." }

Se désabonner
GET /newsletter/unsubscribe/{token} (publique)

token = le token de désabonnement généré à l’inscription.
Supprime l’enregistrement.

Réponse (200 OK)

{ "message": "You have been unsubscribed." }

Liste des abonnés (admin)
GET /admin/subscribers (protégé, admin uniquement)

Réponse (200 OK) : pagination (20 par défaut) des abonnés vérifiés.

{
  "current_page": 1,
  "data": [
    { "id": 1, "email": "user@ex.com", "verified_at": "...", "created_at": "..." }
  ],
  "total": 45
}

Gestion des utilisateurs
Lister tous les utilisateurs
GET /users (protégé, admin uniquement)

Réponse (200 OK) : pagination (20 par défaut)

{
  "data": [
    { "id": 1, "name": "Admin", "email": "admin@ex.com", "is_admin": true, "created_at": "..." }
  ]
}

Créer un utilisateur (admin)
POST /users (protégé, admin uniquement)

Paramètres JSON

{
  "name": "Nouvel User",
  "email": "new@ex.com",
  "password": "secret123",
  "password_confirmation": "secret123",
  "is_admin": false
}

is_admin est optionnel (false par défaut).

Réponse (201 Created)

{
  "message": "Utilisateur créé avec succès",
  "user": { "id": 2, "name": "Nouvel User", "email": "new@ex.com", "is_admin": false }
}

Voir un utilisateur
GET /users/{id} (protégé)

Accès : admin OU l’utilisateur lui-même.

Réponse (200 OK) : champs id, name, email, is_admin, created_at.

Modifier un utilisateur
PUT /users/{id} (protégé)

Accès :

Admin : peut modifier name, email, password, is_admin.

Utilisateur standard (lui-même) : peut modifier name, email et password (nécessite current_password).

Paramètres possibles (tous optionnels) :

{
  "name": "Nouveau nom",
  "email": "newmail@ex.com",
  "current_password": "ancien_mot_de_passe",
  "password": "nouveau_mot_de_passe",
  "password_confirmation": "nouveau_mot_de_passe",
  "is_admin": true
}

Réponse (200 OK)

{
  "message": "Utilisateur mis à jour avec succès",
  "user": { "id": 1, "name": "...", "email": "...", "is_admin": false }
}

Supprimer un utilisateur
DELETE /users/{id} (protégé, admin uniquement)

Un admin ne peut pas supprimer son propre compte.

Réponse (200 OK)

{ "message": "Utilisateur supprimé avec succès" }

Santé
GET /health (publique)

Vérifie la connexion à la base de données.

Réponse (200 OK)

{
  "status": "OK",
  "database": "Connected",
  "timestamp": "2025-01-01 12:00:00"
}

Réponse (500 en cas d’échec)

{
  "status": "ERROR",
  "database": "Disconnected",
  "error": "..."
}