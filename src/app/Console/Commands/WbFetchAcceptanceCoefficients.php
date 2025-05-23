<?php

namespace App\Console\Commands;

use App\Jobs\CheckWarehouseCoefficientsJob;
use Illuminate\Console\Command;

class WbFetchAcceptanceCoefficients extends Command
{
    const DELAY_SECONDS = 15;
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

        foreach ($priorityList as $item) {
            $this->info('Start to handle warehouse: ' . $item["name"]);
            CheckWarehouseCoefficientsJob::dispatch($item['id'])->delay($delay);
            $delay = $delay->addSeconds(self::DELAY_SECONDS);
        }
    }
}
