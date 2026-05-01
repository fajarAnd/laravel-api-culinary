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
php artisan key:generate
php artisan passport:keys
php artisan migrate
php artisan passport:install --uuids
php artisan db:seed --class=AdminUserSeeder
```

## Docker

```bash
docker compose up -d
docker compose exec app php artisan migrate
docker compose exec app php artisan passport:install --uuids
```

## Branching Strategy

Trunk-based development — short-lived branches merged directly into `master`.

| Branch | Purpose |
|--------|---------|
| `master` | Production-ready, always green CI |
| `feature/*` | New features, max 1-2 days |
| `setup/*` | Infrastructure configuration |
| `hotfix/*` | Urgent fixes directly from master |

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register a new user |
| POST | `/api/auth/login` | Login, returns Bearer token |
| POST | `/api/auth/logout` | Logout, revokes token |
| GET | `/api/auth/me` | Get authenticated user |
| POST | `/api/auth/2fa/setup` | Generate 2FA secret + QR code |
| POST | `/api/auth/2fa/enable` | Enable 2FA |
| POST | `/api/auth/2fa/disable` | Disable 2FA |
| POST | `/api/auth/2fa/verify` | Verify OTP (login step 2) |
| GET | `/api/health` | Health check |