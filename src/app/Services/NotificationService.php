<?php

namespace App\Services;

use App\Helpers\WarehouseHelper;
use App\Models\SuitableCoefficient;
use App\Services\Messengers\MessengerInterface;
use App\Services\Messengers\TelegramClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function pushAboutCoefficient(SuitableCoefficient $coefficient): bool
    {
        $message = $this->prepareTextMessage($coefficient);
        $client = $this->getMessengerClient();

        if ($client->send($message)) {
            $coefficient->status = SuitableCoefficient::STATUS_NOTIFIED;
            $coefficient->saveQuietly();

            Log::channel('warehousesCoefficients')->info('Notification has been sent', [
                'id'   => $coefficient->id,
                'sent_at'       => $coefficient->updated_at,
            ]);
            return true;
        }
        return false;
    }

    private function prepareTextMessage(SuitableCoefficient $coefficient): string
    {
        $warehouseName = WarehouseHelper::getNameById($coefficient->warehouse_id);
        $parsedDate = Carbon::parse($coefficient->accept_date)->translatedFormat('d F Y');
        $parsedDateTime = Carbon::parse($coefficient->created_at)->translatedFormat('d F Y \в H:i:s');

        return "*Найден подходящий коэффициент для склада*\n\n" .
            "*__Склад:__* {$warehouseName} \(ID\={$coefficient->warehouse_id}\)\n" .
            "*__Коэффициент:__* {$coefficient->coefficient}\n" .
            "*__Тип поставки:__* {$coefficient->getBoxTypeRussianName()}\n" .
            "*__Дата поставки:__* {$parsedDate}\n" .
            "*__Найден:__* {$parsedDateTime}" ;
    }

    private function getMessengerClient(): MessengerInterface
    {
        return new TelegramClient();
    }
}
