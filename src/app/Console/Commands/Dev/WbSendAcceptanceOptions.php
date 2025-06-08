<?php

namespace App\Console\Commands\Dev;

use App\Services\SuppliesApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

// Чтобы узнать вообще какие типы поставок принимает конкретный склад
class WbSendAcceptanceOptions extends Command
{
    const DELAY_SECONDS = 10;

    /**
     * The name and signature of the console command.
     * @link https://github.com/Dakword/WBSeller/blob/master/docs/Supplies.md
     *
     * @var string
     */
    protected $signature = 'wb:send-acceptance-options';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send acceptance options to Wildberries API';

    /**
     * Execute the console command.
     */
    public function handle(SuppliesApiClient $suppliesApiClient)
    {
        $warehouseId = 301760;
        try {
            $response = $suppliesApiClient->getSupplies()->options([
                ['quantity' => 400, 'barcode' => '2042114272858']
            ], $warehouseId);
            $logData = [
                'warehouseId' => $warehouseId,
                'response' => $response
            ];
            Log::channel('acceptanceOptions')->info('Response:', $logData);
            $this->info('Опции обработаны!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }
}
