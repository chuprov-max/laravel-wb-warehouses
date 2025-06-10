<?php

namespace App\Console\Commands;

use App\Jobs\CheckWarehouseCoefficientsJob;
use Illuminate\Console\Command;

class WbFetchAcceptanceCoefficients extends Command
{
    const DELAY_SECONDS = 10;
    const SEARCH_IS_ACTIVE = true; // temp solution. Need to be activated base on task from admin panel
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
        if (!self::SEARCH_IS_ACTIVE) { // TODO run only when task was activated via admin panel
            return 1;
        }

        try {
            $this->getAcceptanceCoefficients();
            $this->info('Коэффициенты обработаны!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }

    private function getAcceptanceCoefficients()
    {
        $priorityList = config('warehouses.acceptancePriority');
        $delay = now();
        $ids = array_column($priorityList, 'id');

        CheckWarehouseCoefficientsJob::dispatch($ids); // для запуска 1 раз в минуту (раз в 3 минуты)

        /*for ($i = 0; $i < 4; $i++) { // для запуска по 4 раза в минуту
            CheckWarehouseCoefficientsJob::dispatch($ids)->delay($delay);
            $delay = $delay->addSeconds(self::DELAY_SECONDS);
        }*/
    }
}
