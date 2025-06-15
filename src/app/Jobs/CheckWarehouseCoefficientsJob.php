<?php

namespace App\Jobs;

use App\Models\Dto\AcceptanceCoefficientDto;
use App\Models\SearchRequest;
use App\Models\SuitableCoefficient;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SuppliesApiClient;

class CheckWarehouseCoefficientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    public $warehouseIds;

    /**
     * @var SearchRequest|null
     */
    public $searchRequest;

    public function __construct(array $warehouseIds, SearchRequest $searchRequest = null)
    {
        $this->warehouseIds = $warehouseIds;
        $this->searchRequest = $searchRequest;
    }

    public function handle(SuppliesApiClient $suppliesApiClient)
    {
        Log::channel('warehousesCoefficients')->info("Задача запущена", [
            'warehouse_ids' => $this->warehouseIds,
            'time' => now()->toDateTimeString(),
            'searchRequestId' => $this->searchRequest ? $this->searchRequest->id : null,
        ]);
        try {
            $response = $suppliesApiClient->getSupplies()->coefficients($this->warehouseIds);

            Log::channel('warehousesCoefficients')->info('Warehouse coefficients received = '. count($response));

            $suitableCoefficients = $this->filterSuitableCoefficients($response);

            Log::channel('warehousesCoefficients')->info('Suitable coefficients = '. count($suitableCoefficients));

            /** @var AcceptanceCoefficientDto $coefficient */
            foreach ($suitableCoefficients as $coefficient) {
                $model = SuitableCoefficient::create([
                    'warehouse_id' => $coefficient->warehouseId,
                    'search_request_id' => $this->searchRequest ? $this->searchRequest->id : null,
                    'coefficient' => $coefficient->coefficient,
                    'allow_unload' => $coefficient->allowUnload,
                    'box_type_id' => $coefficient->boxTypeId,
                    'accept_date' => Carbon::parse($coefficient->date),
                    'status' => SuitableCoefficient::STATUS_CREATED
                ]);
                Log::channel('warehousesCoefficients')->info('Founded coefficient with ID='.$model->id, $model->toArray());

                $logData = [
                    'warehouse_id'   => $coefficient->warehouseId,
                    'response'       => $response,
                ];
                Log::channel('warehousesCoefficients')->info('Response, which contains suitable coefficient', $logData);

                if ((new NotificationService())->pushAboutCoefficient($model)) {
                    Log::channel('warehousesCoefficients')->info('Telegram Notification sent successfully');
                } else {
                    Log::channel('warehousesCoefficients')->info('Telegram Notification was failed');
                }
            }
        } catch (\Throwable $e) {
            Log::channel('warehousesCoefficients')->error("Ошибка при запросе к складам: " . $e->getMessage());
        }
    }

    private function filterSuitableCoefficients(array $responseData): array
    {
        $suitableCoefficients = [];
        foreach ($responseData as $item) {
            $coefficientDto = new AcceptanceCoefficientDto($item);
            if ($coefficientDto->isSuitable()) {
                $suitableCoefficients[] = $coefficientDto;
            }
        }
        return $suitableCoefficients;
    }
}
