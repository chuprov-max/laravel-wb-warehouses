## Run containers

`docker-compose up -d --build`


## Check containers status

`docker ps`

## Local urls

- Laravel: http://localhost:8080
- phpMyAdmin: http://localhost:8081

## Run queues
`docker exec -it laravel-app php artisan queue:work --sleep=3`

## Enable cron jobs
`crontab -e`

`* * * * * /usr/local/bin/docker exec -u www-data laravel-app php artisan schedule:run >> /dev/null 2>&1`

## Реализуемый функционал
https://seller.wildberries.ru/instructions/ru/ru/subcategory/e324ce0f-9a2a-4b8d-8fd1-72f751b09b3b
