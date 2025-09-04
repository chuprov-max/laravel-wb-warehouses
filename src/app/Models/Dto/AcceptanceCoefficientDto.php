<?php

namespace App\Models\Dto;

use App\Models\SearchRequest;
use Carbon\Carbon;

class AcceptanceCoefficientDto
{
    const HOURS_PLUS_SUITABLE = 1;

    /**
     * @var string
     */
    public $date;

    /**
     * @var int
     */
    public $coefficient;

    /**
     * @var int
     */
    public $warehouseId;

    /**
     * @var bool
     */
    public $allowUnload;

    /**
     * @var int
     */
    public $boxTypeId;

    /**
     * @var string
     */
    public $storageCoef;

    /**
     * @var bool
     */
    public $isSortingCenter;

    public function __construct($responseObject)
    {
        $this->date = $responseObject->date;
        $this->coefficient = $responseObject->coefficient;
        $this->warehouseId = $responseObject->warehouseID;
        $this->allowUnload = $responseObject->allowUnload;
        $this->storageCoef = $responseObject->storageCoef;
        $this->boxTypeId = $responseObject->boxTypeID;
        $this->isSortingCenter = $responseObject->isSortingCenter;
    }

    public function isCoefficientFree(): bool
    {
        return $this->coefficient == 0;
    }

    public function isCoefficientPaid(): bool
    {
        return $this->coefficient >= 1;
    }

    public function isCoefficientDisabled(): bool
    {
        return $this->coefficient < 0;
    }

    public function isSuitable(SearchRequest $searchRequest): bool
    {
        return
            ($this->isCoefficientFree() || $this->isCoefficientPaid())
            && $this->allowUnload
            && $this->boxTypeId == $searchRequest->box_type_id
            && $this->isDateSuitable($searchRequest);
    }

    private function isDateSuitable(SearchRequest $searchRequest): bool
    {
        $parsedDate = Carbon::parse($this->date);

        if (!$searchRequest->date_from && !$searchRequest->date_to) { // не заданы интервалы для поиска
            $threshold = now()->addHours(self::HOURS_PLUS_SUITABLE); // берем все даты от текущего дня
            return $parsedDate->greaterThan($threshold);
        }

        $dateFrom = $searchRequest->date_from ? Carbon::parse($searchRequest->date_from)->startOfDay() : null;
        $dateTo   = $searchRequest->date_to   ? Carbon::parse($searchRequest->date_to)->endOfDay()   : null;

        if ($dateFrom && $dateTo) {
            return $parsedDate->between($dateFrom, $dateTo, true);
        }

        if ($dateFrom) {
            return $parsedDate->greaterThanOrEqualTo($dateFrom);
        }

        if ($dateTo) {
            return $parsedDate->lessThanOrEqualTo($dateTo);
        }

        return true;
    }
}
