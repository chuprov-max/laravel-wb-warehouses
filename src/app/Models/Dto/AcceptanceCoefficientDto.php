<?php

namespace App\Models\Dto;

use App\Models\SearchRequest;
use App\Models\SuitableCoefficient;
use Carbon\Carbon;

class AcceptanceCoefficientDto
{
    const HOURS_PLUS_SUITABLE = 96;

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

    public function isDateSuitable(): bool
    {
        $parsedDate = Carbon::parse($this->date);

        $threshold = now()->addHours(self::HOURS_PLUS_SUITABLE);

        return $parsedDate->greaterThan($threshold);
    }

    public function isSuitable(SearchRequest $searchRequest): bool
    {
        return
            ($this->isCoefficientFree() || $this->isCoefficientPaid())
            && $this->allowUnload
            && $this->boxTypeId == $searchRequest->box_type_id
            && $this->isDateSuitable();
    }
}
