<?php

namespace App\Console\Commands\Dev;

use App\Services\MarketplaceApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use function dd;

class WbCreateSupply extends Command
{
    const DELAY_SECONDS = 10;

    /**
     * The name and signature of the console command.
     * @link https://github.com/Dakword/WBSeller/blob/master/docs/Supplies.md
     *
     * @var string
     */
    protected $signature = 'wb:create-supply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create supply';

    /**
     * Execute the console command.
     */
    public function handle(MarketplaceApiClient $marketplaceApiClient)
    {
        $warehouseId = 301760;
        // WB-GI-160451305  ;  WB-GI-160451649
        //dd($marketplaceApiClient->getMarketplace()->getSupply('WB-GI-160451649'));
        $result = $marketplaceApiClient->getMarketplace()->setDelivery('WB-GI-160451649', "2025-06-10", $warehouseId);
        dd($result);
        // 1. Создание поставки
        $supply = $marketplaceApiClient->getMarketplace()->createSupply('test-2025-06-04');
        if ($supply) {
            dd($supply->id);
        }
        //dd($supply);
        $warehouseId = 301760;
        try {
            $response = $marketplaceApiClient->getSupplies()->options([
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
