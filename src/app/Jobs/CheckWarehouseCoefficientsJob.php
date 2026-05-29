<?php

namespace App\Jobs;

use App\Models\Dto\AcceptanceCoefficientDto;
use App\Models\SearchRequest;
use App\Models\SuitableCoefficient;
use App\Services\NotificationService;
use App\Services\SuppliesApiClient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckWarehouseCoefficientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $warehouseIds,
        public SearchRequest $searchRequest,
    ) {
    }

    public function handle(SuppliesApiClient $suppliesApiClient, NotificationService $notificationService): void
    {
        Log::channel('warehousesCoefficients')->info('Задача запущена', [
            'warehouse_ids'   => $this->warehouseIds,
            'time'            => now()->toDateTimeString(),
            'searchRequestId' => $this->searchRequest->id,
        ]);

        try {
            $response = $suppliesApiClient->getSupplies()->coefficients($this->warehouseIds);

            Log::channel('warehousesCoefficients')->info('Warehouse coefficients received = ' . count($response));

            $suitableCoefficients = $this->filterSuitableCoefficients($response);

            Log::channel('warehousesCoefficients')->info('Suitable coefficients = ' . count($suitableCoefficients));

            $grouped = [];

            /** @var AcceptanceCoefficientDto $coefficient */
            foreach ($suitableCoefficients as $coefficient) {
                $model = SuitableCoefficient::create([
                    'warehouse_id'      => $coefficient->warehouseId,
                    'search_request_id' => $this->searchRequest->id,
                    'coefficient'       => $coefficient->coefficient,
                    'allow_unload'      => $coefficient->allowUnload,
                    'box_type_id'       => $coefficient->boxTypeId,
                    'accept_date'       => Carbon::parse($coefficient->date),
                    'status'            => SuitableCoefficient::STATUS_CREATED,
                ]);

                if ($model) {
                    $grouped[$coefficient->warehouseId][] = $model;
                    Log::channel('warehousesCoefficients')->info('Founded coefficient with ID=' . $model->id, $model->toArray());
                }
            }

            if (empty($grouped)) {
                return;
            }

            $sent = $notificationService->pushAboutCoefficients($this->searchRequest, $grouped);

            Log::channel('warehousesCoefficients')->info(
                $sent
                    ? 'Telegram Notification sent successfully for search ID=' . $this->searchRequest->id
                    : 'Telegram Notification failed for search ID=' . $this->searchRequest->id
            );
        } catch (\Throwable $e) {
            Log::channel('warehousesCoefficients')->error('Ошибка при запросе к складам: ' . $e->getMessage());
        }
    }

    private function filterSuitableCoefficients(array $responseData): array
    {
        $suitable = [];
        foreach ($responseData as $item) {
            $dto = new AcceptanceCoefficientDto($item);
            if ($dto->isSuitable($this->searchRequest)) {
                $suitable[] = $dto;
            }
        }
        return $suitable;
    }
}
