# My Recipes

A Laravel 13 application for creating, browsing, managing, and rating recipes. Authentication, profiles, private recipes, admin moderation, image handling, contact messages, and password resets are included.

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- SQLite support

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
npm install
npm run build
```

For development, run:

```bash
composer run dev
```

## Administrator creation

Create a new administrator without changing an existing account:

```bash
php artisan app:create-admin-user \
  --name="Administrator Name" \
  --email="admin@example.com" \
  --password="A-strong-password"
```

The command requires a password of at least 12 characters and rejects an email that already belongs to a user.

## Tests

```bash
php artisan test
```

The database is rebuilt from migrations during the test run. A development database should use the queue and mail settings from `.env.example`.

## Application overview

- Public recipe browsing and search, with newest/oldest sorting and pagination.
- Authenticated recipe creation, editing, deletion, visibility, and ratings.
- User profiles and public/private recipe visibility rules.
- Administrator account blocking and contact-message routing.
- Local image upload replacement and cleanup for recipe and profile images.
- Password reset workflows and protected authenticated routes.
