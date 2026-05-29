# WB Warehouse Monitor

A Laravel application that monitors Wildberries acceptance coefficients and notifies managers via Telegram when suitable warehouse slots become available.

## How It Works

1. A **scheduler** triggers `wb:fetch-acceptance-coefficients` every 3 minutes.
2. The command loads all active `SearchRequest` records (each defines a box type, warehouse list, date range, and coefficient threshold).
3. For each active request a `CheckWarehouseCoefficientsJob` is dispatched to the queue.
4. The job calls the Wildberries Supplies API, filters coefficients against the search criteria, persists matches to `suitable_coefficients`, and sends a Telegram notification.
5. Requests whose `date_to` has passed are automatically deactivated.

## Tech Stack

- **PHP 8 / Laravel 9**
- **MySQL 8** — main database
- **Laravel Queue** — async job processing
- **Laravel Scheduler** — cron-based command dispatch
- **Docker / Docker Compose** — local development
- **Wildberries Seller API** (`dakword/wbseller`)
- **Telegram Bot API** — manager notifications

## Requirements

- Docker & Docker Compose
- Wildberries Seller API key (Supplies scope)
- Telegram bot token and channel ID

## Local Setup

### 1. Start containers

```bash
docker-compose up -d --build
```

### 2. Install PHP dependencies

```bash
docker exec -it laravel-app composer install
```

### 3. Configure environment

```bash
cp src/.env.example src/.env
docker exec -it laravel-app php artisan key:generate
```

Fill in the required env vars (see [Configuration](#configuration)).

### 4. Run migrations

```bash
docker exec -it laravel-app php artisan migrate
```

### 5. Seed warehouses

Pull the current warehouse list from Wildberries (requires `WILDBERRIES_API_KEY`):

```bash
docker exec -it laravel-app php artisan wb:handle-warehouses
```

### 6. Create the first user

```bash
docker exec -it laravel-app php artisan tinker
```

```php
use App\Models\User;
User::create([
    'name'     => 'admin',
    'email'    => 'admin@example.com',
    'password' => bcrypt('your-password'),
]);
```

### 7. Start the queue worker

```bash
docker exec -it laravel-app php artisan queue:work --sleep=3
```

### 8. Enable the scheduler

Add a cron entry on the host machine:

```bash
crontab -e
```

```
* * * * * /usr/local/bin/docker exec -u www-data laravel-app php artisan schedule:run >> /dev/null 2>&1
```

## Local URLs

| Service    | URL                       |
|------------|---------------------------|
| Application | http://localhost:8080    |
| phpMyAdmin  | http://localhost:8081    |

## Configuration

| Variable              | Description                                    |
|-----------------------|------------------------------------------------|
| `WILDBERRIES_API_KEY` | Wildberries Seller API key (Supplies scope)    |
| `TELEGRAM_BOT_TOKEN`  | Token from @BotFather                          |
| `TELEGRAM_CHAT_ID`    | Telegram channel ID (negative integer)         |

## Docker DNS (macOS)

Docker Desktop on macOS may ignore DNS settings in `docker-compose.yml`. If containers cannot reach external APIs:

1. Open Docker Desktop → Settings → Docker Engine.
2. Add to the JSON config:

```json
{
  "dns": ["8.8.8.8", "1.1.1.1"]
}
```

3. Click **Apply & Restart**.

## Artisan Commands

| Command                              | Description                                             |
|--------------------------------------|---------------------------------------------------------|
| `wb:handle-warehouses`               | Sync warehouse list from Wildberries API to database    |
| `wb:fetch-acceptance-coefficients`   | Dispatch jobs for all active search requests (runs via scheduler) |

## Production: Queue Worker with Supervisor

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart laravel-worker:*
```

## Telegram Setup

### 1. Create a private channel

In Telegram: **New Channel** → set a name → select **Private**.

### 2. Create a bot

Open [@BotFather](https://t.me/BotFather) and run `/newbot`. Copy the token — this is `TELEGRAM_BOT_TOKEN`.

### 3. Add the bot to the channel

Go to channel Settings → Members → Add your bot by username → grant **Post Messages** admin right.

### 4. Get the channel ID

Send any message to the channel, then open:

```
https://api.telegram.org/bot<TELEGRAM_BOT_TOKEN>/getUpdates
```

Find `"chat": { "id": -100xxxxxxxxxx }` in the response — this is `TELEGRAM_CHAT_ID`.

## Frontend Assets

```bash
docker exec -it node-builder npm install
docker exec -it node-builder npm run prod
```

## API Reference

- [Wildberries FBW Supplies API](https://dev.wildberries.ru/openapi/orders-fbw#tag/Postavki)
- [Seller instructions](https://seller.wildberries.ru/instructions/ru/ru/subcategory/e324ce0f-9a2a-4b8d-8fd1-72f751b09b3b)
