<?php

namespace App\Console\Commands;

use Dakword\WBSeller\API\Endpoint\Supplies;
use Illuminate\Console\Command;
use Dakword\WBSeller\API;

class WbFetchAcceptanceCoefficients extends AbstractWbCommand
{
    /**
     * The name and signature of the console command.
     * @link https://github.com/Dakword/WBSeller/blob/master/docs/Supplies.md
     *
     * @var string
     */
    protected $signature = 'wb:fetch-acceptance-coefficients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch acceptance coefficients from Wildberries API';

    /**
     * Execute the console command.
     */
    public function customHandle()
    {
        try {
            $supplies = $this->wbSellerApi->Supplies();

            // Отправка запроса к API для получения коэффициентов
            $response = $supplies->ping(); //$wbSeller->get('https://supplies-api.wildberries.ru/api/v1/acceptance/coefficients');
            if ($response) {
                //$this->getWarehouses($supplies);
                $this->getAcceptanceCoefficients($supplies);
                die();
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

    private function getWarehouses(Supplies $supplies)
    {
        // TODO save warehouses to database
        dd($supplies->warehouses());
    }

    private function getAcceptanceCoefficients(Supplies $supplies)
    {
        dd($supplies->coefficients([507]));
    }

    private function setAcceptanceOptions(Supplies $supplies)
    {
        dd($supplies->options());
    }
}
