# Beauty Salon Backend (Laravel 12 + Filament v3)

Production-ready backend for a beauty salon with clean layered architecture, API for frontend, RBAC, and Filament admin panel.

## Stack
- PHP 8.2+
- Laravel 12
- MySQL
- Redis (cache/queue)
- Sanctum API auth
- Filament v3 admin panel
- Spatie Laravel Permission

## Architecture
Project is split into explicit layers:
- `app/Domain` - entities/models and enums
- `app/Application` - services/use-cases (`AuthService`, `AppointmentService`, `SlotGenerationService`)
- `app/Infrastructure` - notifications/integrations (`TelegramWebhookClient`, appointment notifier)
- `app/Http` - controllers, form requests, API resources
- `app/Admin` - Filament resources/pages/widgets

## Features (MVP)
- Auth: register/login/logout/me
- Roles: `admin`, `manager`, `master`, `client`
- Catalog: categories, services, masters
- Appointments:
  - create pending appointment
  - list client appointments
  - cancel appointment
  - overlap prevention for master schedule
  - status transition validation
  - multi-service booking (`service_ids[]`)
- Slot generation by master schedule rules
- Notifications on appointment creation:
  - log
  - Telegram webhook (optional)
  - email to client (optional)

## Admin Panel (`/admin`)
CRUD for:
- Categories
- Services
- Masters
- Appointments
- Users + roles

RBAC behavior:
- `admin`: full access
- `manager`: all except admin-role management
- `master`: own appointments + own profile

## Quick Start (local without Docker)
1. Install dependencies:
```bash
composer install
cp .env.example .env
php artisan key:generate
```

2. Configure `.env` (MySQL/Redis).

3. Run database:
```bash
php artisan migrate --seed
```

4. Start app:
```bash
php artisan serve
```

5. Open admin:
- `http://127.0.0.1:8000/admin`
- admin login: `admin@gmail.com` / `password`

## Docker Run
```bash
docker compose up -d --build
docker compose exec php composer install
docker compose exec php cp .env.example .env
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate --seed
```

Application URL: `http://localhost:8080`

## API Endpoints
- `GET /api/categories`
- `GET /api/services?category_id=`
- `GET /api/masters?service_id=`
- `GET /api/masters?service_ids[]=1&service_ids[]=2`
- `GET /api/slots?service_ids[]=1&master_id=&date=`
- `POST /api/appointments` (guest allowed)
- `GET /api/appointments/my`
- `PATCH /api/appointments/{id}/cancel`

Auth:
- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`

Postman collection:
- `docs/postman_collection.json`

## Tests
```bash
php artisan test
```

## Deploy Notes
- Set `APP_ENV=production`, `APP_DEBUG=false`
- Configure queue worker (Redis recommended)
- Configure `TELEGRAM_WEBHOOK_URL` and `APPOINTMENT_SEND_EMAIL_TO_CLIENT=true` if needed
- Run migrations and optimize:
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
