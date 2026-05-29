<?php

namespace App\Providers;

use App\Services\Messengers\MessengerInterface;
use App\Services\Messengers\TelegramClient;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MessengerInterface::class, TelegramClient::class);
    }

    public function boot(): void
    {
        Carbon::setLocale('ru');
        setlocale(LC_TIME, 'ru_RU.UTF-8');
    }
}
