# API Documentation – Blog & Newsletter System

## Introduction

This API allows you to manage articles, comments, images, user authentication, and a newsletter subscription system.  
Authentication is handled via Laravel Sanctum (Bearer token).  
Some endpoints require admin privileges (is_admin = true).

Base URL: http://your-domain.com/api (adjust to your environment)

---

## Authentication

| Method | Endpoint          | Description                |
|--------|-------------------|----------------------------|
| POST   | /register         | Create a new user account  |
| POST   | /login            | Login and receive a token  |
| POST   | /logout           | Revoke current token       |
| GET    | /me               | Get authenticated user     |

All protected endpoints must include the header:  
Authorization: Bearer {token}

---

### 1. Register

POST /api/register
Content-Type: application/json

Request body:
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secret123",
    "password_confirmation": "secret123"
}

Response (201 Created):
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "is_admin": false,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "1|abcdef..."
}

### 2. Login

POST /api/login
Content-Type: application/json

Request body:
{
    "email": "john@example.com",
    "password": "secret123"
}

Response (200 OK):
{
    "message": "Connexion réussie",
    "token": "1|abcdef...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "is_admin": false
    }
}

### 3. Logout

POST /api/logout
Authorization: Bearer {token}

Response (200 OK):
{
    "message": "Déconnecté"
}

### 4. Get current user

GET /api/me
Authorization: Bearer {token}

Response (200 OK):
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "is_admin": false,
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
}

---

## Articles

All article endpoints require authentication.

| Method | Endpoint                    | Description                         |
|--------|-----------------------------|-------------------------------------|
| GET    | /articles                   | List articles (paginated)           |
| POST   | /articles                   | Create a new article                |
| GET    | /articles/{article}         | Show a single article               |
| PUT    | /articles/{article}         | Update an article                   |
| DELETE | /articles/{article}         | Delete an article                   |

### 1. List articles

GET /api/articles?page=1
Authorization: Bearer {token}

- Admin → sees all articles (draft + published)
- Normal user → sees own articles (any status) + published articles from others

Response (200 OK): Paginated list of articles with user, comments.user, images relations.

{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "title": "My first article",
            "slug": "my-first-article",
            "content": "Full content...",
            "excerpt": "Short summary",
            "cover_image": "covers/abc.jpg",
            "status": "published",
            "published_at": "2025-01-01T10:00:00.000000Z",
            "created_at": "...",
            "updated_at": "...",
            "user": { ... },
            "comments": [...],
            "images": [...]
        }
    ],
    "per_page": 10,
    "total": 25
}

### 2. Create article

POST /api/articles
Authorization: Bearer {token}
Content-Type: multipart/form-data

Request parameters:
- title (string, required, max 255)
- content (string, required)
- excerpt (string, optional)
- cover_image (file, optional, image max 2MB)
- status (string, required, draft or published)

If status = published, published_at is set automatically.

Response (201 Created):
{
    "id": 2,
    "user_id": 1,
    "title": "New article",
    "slug": "new-article",
    "content": "...",
    "excerpt": null,
    "cover_image": "covers/xyz.jpg",
    "status": "published",
    "published_at": "2025-01-02T12:00:00.000000Z",
    "created_at": "...",
    "updated_at": "..."
}

### 3. Show single article

GET /api/articles/{article}
Authorization: Bearer {token}

Access rules:
- Published articles → anyone can view
- Draft articles → only author or admin

Response (200 OK): full article with user, comments.user, images.

### 4. Update article

PUT /api/articles/{article}
Authorization: Bearer {token}
Content-Type: multipart/form-data (or application/json if no file)

Request parameters: same as create, all fields optional.

If status changes from draft to published, published_at is set to now.
If a new cover_image is uploaded, the old one is deleted.

Response (200 OK): updated article object.

### 5. Delete article

DELETE /api/articles/{article}
Authorization: Bearer {token}

Also deletes:
- All ArticleImage records associated
- Physical image files (cover + extra images)

Response (200 OK):
{
    "message": "Article deleted"
}

---

## Comments

| Method | Endpoint                         | Description               |
|--------|----------------------------------|---------------------------|
| GET    | /articles/{article}/comments     | List comments for article |
| POST   | /articles/{article}/comments     | Add a comment             |
| PUT    | /comments/{comment}              | Update a comment          |
| DELETE | /comments/{comment}              | Delete a comment          |

### 1. List comments of an article

GET /api/articles/{article}/comments

Response (200 OK): array of comments with user relation.

[
    {
        "id": 1,
        "article_id": 1,
        "user_id": 2,
        "content": "Great post!",
        "created_at": "...",
        "updated_at": "...",
        "user": {
            "id": 2,
            "name": "Jane Doe",
            "email": "jane@example.com"
        }
    }
]

### 2. Add a comment

POST /api/articles/{article}/comments
Authorization: Bearer {token}
Content-Type: application/json

Request body:
{
    "content": "This is my comment"
}

Response (201 Created):
{
    "id": 2,
    "article_id": 1,
    "user_id": 1,
    "content": "This is my comment",
    "created_at": "...",
    "updated_at": "...",
    "user": { ... }
}

### 3. Update a comment

PUT /api/comments/{comment}
Authorization: Bearer {token}
Content-Type: application/json

Permission: comment author or admin only.

Request body:
{
    "content": "Updated comment text"
}

Response (200 OK): updated comment.

### 4. Delete a comment

DELETE /api/comments/{comment}
Authorization: Bearer {token}

Permission: comment author or admin only.

Response (200 OK):
{
    "message": "Comment deleted"
}

---

## Media (Extra Images)

| Method | Endpoint                               | Description                      |
|--------|----------------------------------------|----------------------------------|
| POST   | /articles/{article}/images             | Upload an extra image for article|
| DELETE | /images/{image}                        | Delete an extra image            |
| GET    | /articles/{article}/images             | Get all extra images of article  |

### 1. Upload extra image

POST /api/articles/{article}/images
Authorization: Bearer {token}
Content-Type: multipart/form-data

Permission: article author or admin.

Request field:
- image (file, required, max 2MB)

Response (201 Created):
{
    "id": 1,
    "article_id": 1,
    "image_path": "articles/abc.jpg",
    "created_at": "...",
    "updated_at": "..."
}

### 2. Delete extra image

DELETE /api/images/{image}
Authorization: Bearer {token}

Permission: article author or admin.
Also deletes the physical file from storage.

Response (200 OK):
{
    "message": "Image deleted"
}

### 3. Get all extra images of an article

GET /api/articles/{article}/images

Response (200 OK):
[
    {
        "id": 1,
        "article_id": 1,
        "image_path": "articles/abc.jpg",
        "created_at": "...",
        "updated_at": "..."
    }
]

---

## Newsletter Subscription

| Method | Endpoint                                 | Description                        |
|--------|------------------------------------------|------------------------------------|
| POST   | /newsletter/subscribe                    | Subscribe with email confirmation  |
| GET    | /newsletter/confirm/{subscriber}         | Confirm subscription (via link)    |
| GET    | /newsletter/unsubscribe/{token}          | Unsubscribe (via link)             |
| GET    | /admin/newsletter/subscribers            | List subscribers (admin only)      |

### 1. Subscribe (request confirmation)

POST /api/newsletter/subscribe
Content-Type: application/json

Request body:
{
    "email": "user@example.com"
}

Response (200 OK):
{
    "message": "Please check your email to confirm subscription.",
    "subscriber": {
        "email": "user@example.com",
        "created_at": "2025-01-02T10:00:00.000000Z"
    }
}

An email is sent to the address with a confirmation link.

### 2. Confirm subscription

GET /api/newsletter/confirm/{subscriber}

Example: /api/newsletter/confirm/5

Response (200 OK):
{
    "message": "Subscription confirmed."
}

If already confirmed: "message": "Already confirmed"

### 3. Unsubscribe

GET /api/newsletter/unsubscribe/{token}

Example: /api/newsletter/unsubscribe/6f4a8d2e9c...

Response (200 OK):
{
    "message": "You have been unsubscribed."
}

### 4. List subscribers (Admin only)

GET /api/admin/newsletter/subscribers?page=1
Authorization: Bearer {token_of_admin}

Response (200 OK): paginated list of verified subscribers.

{
    "current_page": 1,
    "data": [
        {
            "id": 3,
            "email": "user@example.com",
            "unsubscribe_token": "6f4a8d2e9c...",
            "verified_at": "2025-01-02T10:05:00.000000Z",
            "created_at": "2025-01-02T10:00:00.000000Z",
            "updated_at": "2025-01-02T10:05:00.000000Z"
        }
    ],
    "per_page": 20,
    "total": 45
}

---

## Error Handling

Common HTTP status codes:
- 200 – OK
- 201 – Created
- 403 – Unauthorized (missing permission)
- 422 – Validation error (field format, unique, required, etc.)

Validation error example (422):
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["The email has already been taken."]
    }
}

Unauthorized example (403):
{
    "message": "Unauthorized"
}

---

## Notes

- All timestamps are in UTC and follow ISO 8601 format.
- The slug field is automatically generated from the title and is unique per article.
- File uploads are stored in the public disk (symbolic link required: php artisan storage:link).
- Subscriber confirmation uses a simple id in the URL; you may want to replace it with a signed URL in production for extra security.

---

## Database Schema (Simplified)

users – id, name, email, password, is_admin, timestamps
articles – id, user_id, title, slug, content, excerpt, cover_image, status, published_at, timestamps
comments – id, article_id, user_id, content, timestamps
article_images – id, article_id, image_path, timestamps
subscribers – id, email, unsubscribe_token, verified_at, timestamps

*Cette API est faite en Laravel 13 et suit toutes les conventions REST.*