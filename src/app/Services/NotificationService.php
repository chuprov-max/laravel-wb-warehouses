<?php

namespace App\Services;

use App\Helpers\StringHelper;
use App\Models\SearchRequest;
use App\Models\SuitableCoefficient;
use App\Models\Warehouse;
use App\Services\Messengers\MessengerInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(private MessengerInterface $messenger)
    {
    }

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

        Log::channel('warehousesCoefficients')->info('Telegram Notification was prepared', [
            'message' => $message,
        ]);

        if ($this->messenger->send($message)) {
            foreach ($coefficients as $coefficientsForWarehouse) {
                foreach ($coefficientsForWarehouse as $coefficient) {
                    $coefficient->status = SuitableCoefficient::STATUS_NOTIFIED;
                    $coefficient->saveQuietly();
                }
            }

            Log::channel('warehousesCoefficients')->info('Notification has been sent', [
                'number' => count($coefficients),
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
}
