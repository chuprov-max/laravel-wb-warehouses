<?php

namespace App\Services;

use App\Helpers\StringHelper;
use App\Models\SearchRequest;
use App\Models\SuitableCoefficient;
use App\Models\Warehouse;
use App\Services\Messengers\MessengerInterface;
use App\Services\Messengers\TelegramClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * @param SearchRequest $searchRequest
     * @param array $coefficients
     * @return bool
     */
    public function pushAboutCoefficients(SearchRequest $searchRequest, array $coefficients): bool
    {
        $boxTypeTitle = SuitableCoefficient::getBoxTypeRussianNameById($searchRequest->box_type_id);
        $message = "*Найден подходящий коэффициент для поиска*\n\n" .
            "*__ID поиска:__* {$searchRequest->id}\n" .
            "*__Тип поставки:__* {$boxTypeTitle}\n\n";

        foreach ($coefficients as $warehouseId => $coefficientsForWarehouse) {
            $warehouseName = StringHelper::escapeMarkdown(Warehouse::getNameByWbId($warehouseId));
            $message .= "\n📦 *__Склад:__* {$warehouseName}\n";
            foreach ($coefficientsForWarehouse as $coefficient) {
                $message .= $this->prepareTextByWarehouse($coefficient);
            }
        }

        $client = $this->getMessengerClient();

        Log::channel('warehousesCoefficients')->info('Telegram Notification was prepared', [
            'message'   => $message,
        ]);

        if ($client->send($message)) {
            foreach ($coefficients as $coefficientsForWarehouse) {
                foreach ($coefficientsForWarehouse as $coefficient) {
                    $coefficient->status = SuitableCoefficient::STATUS_NOTIFIED;
                    $coefficient->saveQuietly();
                }
            }

            Log::channel('warehousesCoefficients')->info('Notification has been sent', [
                'number'   => count($coefficients),
            ]);

            return true;
        }
        return false;
    }

    /**
     * @deprecated
     */
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

    private function prepareTextByWarehouse(SuitableCoefficient $coefficient): string
    {
        $parsedDate = Carbon::parse($coefficient->accept_date)->translatedFormat('d F Y');

        return "— Коэффициент: {$coefficient->coefficient} \| Дата поставки: {$parsedDate}\n";
    }

    private function prepareTextMessage(SuitableCoefficient $coefficient): string
    {
        $warehouseName = Warehouse::getNameByWbId($coefficient->warehouse_id);
        $parsedDate = Carbon::parse($coefficient->accept_date)->translatedFormat('d F Y');
        $parsedDateTime = Carbon::parse($coefficient->created_at)->translatedFormat('d F Y \в H:i:s');

        return "*Найден подходящий коэффициент для склада*\n\n" .
            "*__Склад:__* {$warehouseName}\n" .
            "*__ID склада:__* {$coefficient->warehouse_id}\n" .
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
