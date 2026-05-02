# Culinary Bot API

[![CI](https://github.com/fajarAnd/laravel-api-culinary/actions/workflows/ci.yml/badge.svg)](https://github.com/fajarAnd/laravel-api-culinary/actions/workflows/ci.yml)

REST API for a Telegram culinary bot — restaurant search, menus, and reviews built with Laravel 12 + PostgreSQL.

## Requirements

- PHP 8.2+
- PostgreSQL 16
- Redis 7
- Docker & Docker Compose

## How To Run
### Local
```bash
cp .env.example .env
composer install
php artisan migrate --seed
php artisan passport:keys
php artisan serve
```

### Docker

```bash
cp .env.example .env
docker compose up -d
# app automatically runs migrate, seed, passport:keys, then serves on port 8000
```

## Testing

### Unit Tests

```bash
php artisan test
# or run a specific suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

### 2FA Flow (step by step)

**Prerequisites:** logged-in user with a valid `access_token`.

**1. Setup — generate a secret & QR code**
```
POST /api/2fa/setup
Authorization: Bearer <access_token>
```
Response includes `secret` and `qr_code_url`. Scan the QR code with an authenticator app (Google Authenticator, Authy, etc.) or enter the `secret` manually.

**2. Enable — confirm OTP to activate 2FA**
```
POST /api/2fa/enable
Authorization: Bearer <access_token>
Body: { "otp": "123456" }
```
Use the 6-digit code from your authenticator app.

**3. Login with 2FA enabled**
```
POST /api/login
Body: { "email": "...", "password": "..." }
```
When 2FA is active, the response returns `requires_2fa: true` and a short-lived `tmp_token` instead of a full token.

**4. Verify OTP — exchange `tmp_token` for a full token**
```
POST /api/2fa/verify
Authorization: Bearer <tmp_token>
Body: { "otp": "123456" }
```
On success, returns a full `access_token`. The `tmp_token` is revoked.

**5. Disable 2FA**
```
POST /api/2fa/disable
Authorization: Bearer <access_token>
Body: { "otp": "123456" }
```

---

## API Endpoints

Import Postman collection for all endpoints: `postman/collection.json` + `postman/environment.json`.

Set `base_url` in Postman environment to `http://localhost:8000`, then run **Login** to auto-populate `api_token` for authenticated requests.

---

> **Note:**
> - `.env.example` contains development OAuth secrets. For production, replace with `php artisan passport:install`.
> - `/api/restaurants/*` endpoints use mock/stub data since Zomato API is unavailable. 