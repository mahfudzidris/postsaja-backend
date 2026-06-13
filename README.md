# PostSaja Backend

Telegram bot backend for PostSaja — AI marketing assistant for SME Malaysia.

## Tech Stack

- Laravel 12
- Telegram Bot SDK (irazasyed/telegram-bot-sdk)
- MySQL

## Setup

1. Clone repo
2. `cp .env.example .env` — fill in DB credentials + `TELEGRAM_BOT_TOKEN`
3. `composer install`
4. `php artisan migrate`
5. `php artisan db:seed --class=PostsajaDemoSeeder`
6. Deploy to Laravel Cloud
7. Visit `https://your-app.laravel.cloud/api/telegram/set-webhook`

## Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| POST | `/api/telegram/webhook` | Telegram webhook receiver |
| GET | `/api/telegram/set-webhook` | Register webhook with Telegram |
| GET | `/api/health` | Health check |

## Demo Business Codes

- `BENGKEL` — Bengkel Demo Khamis
- `MAKAN` — Kedai Makan Demo
