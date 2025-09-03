<?php

namespace App\Console\Commands;

use App\Jobs\CheckWarehouseCoefficientsJob;
use App\Models\SearchRequest;
use Illuminate\Console\Command;

class WbFetchAcceptanceCoefficients extends Command
{
    const DELAY_SECONDS = 10;

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
    public function handle()
    {
        $requests = SearchRequest::getCurrentActiveRequests();

        if ($requests->isEmpty()) { // нет активного запроса на поиск => поиск не запускаем
            $this->info('Не задано активных поисков!');
            return 1;
        }

        try {
            foreach ($requests as $request) {
                $this->getAcceptanceCoefficients($request);
                $this->info("Коэффициенты для поиска #{$request->id} обработан");
            }

            $this->info('Коэффициенты обработаны!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }

    private function getAcceptanceCoefficients(SearchRequest $searchRequest)
    {
        //$delay = now();

        CheckWarehouseCoefficientsJob::dispatch($searchRequest->warehouses, $searchRequest); // для запуска 1 раз в минуту (раз в 3 минуты)

        /*for ($i = 0; $i < 4; $i++) { // для запуска по 4 раза в минуту
            CheckWarehouseCoefficientsJob::dispatch($ids)->delay($delay);
            $delay = $delay->addSeconds(self::DELAY_SECONDS);
        }*/
    }
}
