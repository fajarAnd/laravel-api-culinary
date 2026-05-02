# Culinary Bot API

[![CI](https://github.com/fajarAnd/laravel-api-culinary/actions/workflows/ci.yml/badge.svg)](https://github.com/fajarAnd/laravel-api-culinary/actions/workflows/ci.yml)

REST API for a Telegram culinary bot — restaurant search, menus, and reviews built with Laravel 12 + PostgreSQL.

## Requirements

- PHP 8.2+
- PostgreSQL 16
- Redis 7
- Docker & Docker Compose

## Setup

```bash
cp .env.example .env
composer install
php artisan migrate --seed
php artisan passport:keys
php artisan serve
```

## Docker

```bash
cp .env.example .env
docker compose up -d
# app automatically runs migrate, seed, passport:keys, then serves on port 8000
```

## API Endpoints

Import Postman collection for all endpoints: `postman/collection.json` + `postman/environment.json`.

Set `base_url` in Postman environment to `http://localhost:8000`, then run **Login** to auto-populate `api_token` for authenticated requests.

---

> **Note:**
> - `.env.example` contains development OAuth secrets. For production, replace with `php artisan passport:install`.
> - `/api/restaurants/*` endpoints use mock/stub data since Zomato API is unavailable. 