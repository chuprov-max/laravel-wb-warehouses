## Local installation
#### Run containers
`docker-compose up -d --build`

#### Install packages
`docker exec -it laravel-app composer install`  

#### Generate Laravel Password
`docker exec -it laravel-app php artisan key:generate` 

#### Run migrations
`docker exec -it laravel-app php artisan migrate` 

#### Create first user
`docker exec -it laravel-app php artisan tinker`

```
> use App\Models\User;
User::create([
'name' => 'admin',
'email' => 'mail@englishteachers.ru',
'password' => bcrypt('PASSWORD'),
]);
```

#### Add env variables
 - `WILDBERRIES_API_KEY`
 - `TELEGRAM_BOT_TOKEN`
 - `TELEGRAM_CHAT_ID`

## Check containers status

`docker ps`

Docker Desktop (Mac) может игнорировать DNS в docker-compose.yml. 
- Перейдите в Docker Desktop → Settings → Docker Engine 
- Добавьте в JSON:
```
{
  "dns": ["8.8.8.8", "1.1.1.1"]
}
```

Нажмите Apply & Restart.

## Local urls

- Laravel: http://localhost:8080
- phpMyAdmin: http://localhost:8081

## Prod urls
- https://titulwb.ru/
- https://95.163.229.231/phpmyadmin

## Run queues
`docker exec -it laravel-app php artisan queue:work --sleep=3`

## Enable cron jobs
`crontab -e`

`* * * * * /usr/local/bin/docker exec -u www-data laravel-app php artisan schedule:run >> /dev/null 2>&1`

### List current cron jobs
```crontab -e```  
comment if don't need anymore


## Build frontend
```npm install && npm run prod```

## Реализуемый функционал
https://seller.wildberries.ru/instructions/ru/ru/subcategory/e324ce0f-9a2a-4b8d-8fd1-72f751b09b3b

## Техническая документация по API (FBW Поставки)
https://dev.wildberries.ru/openapi/orders-fbw#tag/Postavki

### Перезапуск очередей
```
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart laravel-worker:*
```

## 📬 Настройка отправки сообщений в Telegram-канал

Для интеграции Laravel-проекта с Telegram нужно получить два параметра:

- `TELEGRAM_BOT_TOKEN`
- `TELEGRAM_CHAT_ID`

### ✅ Шаг 1: Создание закрытого канала

1. В Telegram нажмите **«Создать канал»**.
2. Укажите имя, например `My Logs`.
3. Выберите **Тип канала: Частный**.
4. Завершите создание.

---

### ✅ Шаг 2: Создание Telegram-бота

1. Откройте чат с [`@BotFather`](https://t.me/BotFather).
2. Отправьте команду:

    ```
    /newbot
    ```

3. Укажите имя и username бота, например: `my_logger_bot`.
4. После создания вы получите токен:

    ```
    123456789:AAHqRANDOMtextKEYdfgdfgdfg
    ```

➡️ Сохраните его как `TELEGRAM_BOT_TOKEN`.

---

### ✅ Шаг 3: Добавление бота в канал

1. Перейдите в настройки канала → **Участники**.
2. Нажмите **Добавить участника**, найдите своего бота по username (`@my_logger_bot`).
3. Добавьте его и назначьте **администратором** (достаточно права публиковать сообщения).

---

### ✅ Шаг 4: Получение `TELEGRAM_CHAT_ID`

1. Отправьте любое сообщение в канал.
2. Откройте в браузере ссылку:

    ```
    https://api.telegram.org/bot<TELEGRAM_BOT_TOKEN>/getUpdates
    ```

   Пример:

    ```
    https://api.telegram.org/bot123456789:ABCdefGHIjklMNOpqrSTUvwXYZ12345678/getUpdates
    ```

3. В ответе найдите блок:

    ```json
    "chat": {
      "id": -1001234567890,
      "title": "My Logs",
      ...
    }
    ```

➡️ Значение `id` и есть `TELEGRAM_CHAT_ID`.

---

### 🧾 Пример `.env`

```env
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrSTUvwXYZ12345678
TELEGRAM_CHAT_ID=-1001234567890
