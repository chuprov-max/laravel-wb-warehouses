<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuitableCoefficient;
use App\Services\NotificationService;

class TestTelegramNotification extends Command
{
    protected $signature = 'telegram:test-notification {--id= : ID коэффициента}';
    protected $description = 'Отправить тестовое уведомление в Telegram по ID SuitableCoefficient';

    public function handle()
    {
        $id = $this->option('id');

        if (!$id) {
            $this->error('Укажите параметр --id');
            return 1;
        }

        $model = SuitableCoefficient::find($id);

        if (!$model) {
            $this->error("Коэффициент с ID {$id} не найден.");
            return 1;
        }

        (new NotificationService())->pushAboutCoefficient($model);

        $this->info("Уведомление отправлено по коэффициенту ID = {$id}");

        return 0;
    }
}
