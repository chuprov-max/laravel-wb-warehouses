<?php

namespace App\Console\Commands;

use Dakword\WBSeller\API\Endpoint\Supplies;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Dakword\WBSeller\API;

class WbHandleWarehouses extends AbstractWbCommand
{
    /**
     * The name and signature of the console command.
     * @link https://github.com/Dakword/WBSeller/blob/master/docs/Supplies.md
     *
     * @var string
     */
    protected $signature = 'wb:handle-warehouses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get list of warehouses from Wildberries API and insert records to database';

    /**
     * Execute the console command.
     */
    public function customHandle()
    {
        try {
            $supplies = $this->wbSellerApi->Supplies();

            // Отправка запроса к API для получения коэффициентов
            $ping = $supplies->ping(); //$wbSeller->get('https://supplies-api.wildberries.ru/api/v1/acceptance/coefficients');
            if ($ping && $ping->Status == 'OK') {
                $this->syncWarehouses($supplies);
            }

            // Проверка ответа
            if ($response['error'] ?? false) {
                $this->error("Ошибка API: " . $response['message'] ?? 'Неизвестная ошибка');
                return 1;
            }

            // Вывод данных
            $coefficients = $response['data'] ?? [];
            foreach ($coefficients as $coefficient) {
                $this->info("Склад: {$coefficient['warehouse']}, Коэффициент: {$coefficient['coefficient']}");
            }

            $this->info('Коэффициенты успешно получены!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }

    private function syncWarehouses(Supplies $supplies): bool
    {
        $processedCount = 0;
        foreach ($supplies->warehouses() as $item) {
            Warehouse::updateOrCreate(
                ['wb_id' => $item->ID], // если `id` должен соответствовать внешнему ID
                [
                    'wb_id' => $item->ID,
                    'name'        => $item->name,
                    'address'     => $item->address,
                    'work_time'   => $item->workTime,
                    'accepts_qr'  => $item->acceptsQR,
                    'active'      => $item->isActive,
                ]
            );
            $processedCount++;
        }
        echo "Number of handled warehouses: $processedCount";
        return true;
    }
}
