<?php

namespace App\Console\Commands;

use App\Services\SuppliesApiClient;
use App\Models\Warehouse;
use Illuminate\Console\Command;

class WbHandleWarehouses extends Command
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
    public function handle(SuppliesApiClient $suppliesApiClient)
    {
        $processedCount = 0;
        try {
            $warehouses = $suppliesApiClient->getSupplies()->warehouses();
            foreach ($warehouses as $item) {
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
            $this->info("Number of handled warehouses: {$processedCount} \n");
            return true;
        } catch (\Exception $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }
}
