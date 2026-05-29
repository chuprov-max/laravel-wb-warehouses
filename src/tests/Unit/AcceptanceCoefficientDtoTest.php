<?php

namespace Tests\Unit;

use App\Models\Dto\AcceptanceCoefficientDto;
use App\Models\SearchRequest;
use Tests\TestCase;

class AcceptanceCoefficientDtoTest extends TestCase
{
    private function makeDto(array $overrides = []): AcceptanceCoefficientDto
    {
        return new AcceptanceCoefficientDto((object) array_merge([
            'date'            => now()->addDays(3)->toDateTimeString(),
            'coefficient'     => 0,
            'warehouseID'     => 1,
            'allowUnload'     => true,
            'storageCoef'     => '1.0',
            'boxTypeID'       => 2,
            'isSortingCenter' => false,
        ], $overrides));
    }

    private function makeSearchRequest(array $overrides = []): SearchRequest
    {
        $request = new SearchRequest();
        $request->box_type_id = 2;
        $request->date_from   = null;
        $request->date_to     = null;

        foreach ($overrides as $key => $value) {
            $request->$key = $value;
        }

        return $request;
    }

    public function test_disabled_coefficient_is_not_suitable(): void
    {
        $this->assertFalse(
            $this->makeDto(['coefficient' => -1])->isSuitable($this->makeSearchRequest())
        );
    }

    public function test_free_coefficient_is_suitable(): void
    {
        $this->assertTrue(
            $this->makeDto(['coefficient' => 0])->isSuitable($this->makeSearchRequest())
        );
    }

    public function test_paid_coefficient_is_suitable(): void
    {
        $this->assertTrue(
            $this->makeDto(['coefficient' => 5])->isSuitable($this->makeSearchRequest())
        );
    }

    public function test_not_suitable_when_unload_is_not_allowed(): void
    {
        $this->assertFalse(
            $this->makeDto(['allowUnload' => false])->isSuitable($this->makeSearchRequest())
        );
    }

    public function test_not_suitable_when_box_type_does_not_match(): void
    {
        $this->assertFalse(
            $this->makeDto(['boxTypeID' => 5])->isSuitable($this->makeSearchRequest(['box_type_id' => 2]))
        );
    }

    public function test_suitable_when_no_date_range_and_slot_is_far_enough(): void
    {
        $dto = $this->makeDto(['date' => now()->addHours(2)->toDateTimeString()]);

        $this->assertTrue($dto->isSuitable($this->makeSearchRequest()));
    }

    public function test_not_suitable_when_no_date_range_and_slot_is_within_threshold(): void
    {
        $dto = $this->makeDto(['date' => now()->addMinutes(30)->toDateTimeString()]);

        $this->assertFalse($dto->isSuitable($this->makeSearchRequest()));
    }

    public function test_suitable_when_date_falls_within_range(): void
    {
        $dto = $this->makeDto(['date' => now()->addDays(3)->toDateTimeString()]);
        $request = $this->makeSearchRequest([
            'date_from' => now()->addDay()->toDateString(),
            'date_to'   => now()->addDays(5)->toDateString(),
        ]);

        $this->assertTrue($dto->isSuitable($request));
    }

    public function test_not_suitable_when_date_falls_outside_range(): void
    {
        $dto = $this->makeDto(['date' => now()->addDays(10)->toDateTimeString()]);
        $request = $this->makeSearchRequest([
            'date_from' => now()->addDay()->toDateString(),
            'date_to'   => now()->addDays(5)->toDateString(),
        ]);

        $this->assertFalse($dto->isSuitable($request));
    }

    public function test_suitable_when_only_date_from_set_and_slot_is_after(): void
    {
        $dto = $this->makeDto(['date' => now()->addDays(5)->toDateTimeString()]);
        $request = $this->makeSearchRequest(['date_from' => now()->addDays(3)->toDateString()]);

        $this->assertTrue($dto->isSuitable($request));
    }

    public function test_not_suitable_when_only_date_from_set_and_slot_is_before(): void
    {
        $dto = $this->makeDto(['date' => now()->addDay()->toDateTimeString()]);
        $request = $this->makeSearchRequest(['date_from' => now()->addDays(3)->toDateString()]);

        $this->assertFalse($dto->isSuitable($request));
    }

    public function test_suitable_when_only_date_to_set_and_slot_is_before(): void
    {
        $dto = $this->makeDto(['date' => now()->addDays(2)->toDateTimeString()]);
        $request = $this->makeSearchRequest(['date_to' => now()->addDays(5)->toDateString()]);

        $this->assertTrue($dto->isSuitable($request));
    }

    public function test_not_suitable_when_only_date_to_set_and_slot_is_after(): void
    {
        $dto = $this->makeDto(['date' => now()->addDays(10)->toDateTimeString()]);
        $request = $this->makeSearchRequest(['date_to' => now()->addDays(5)->toDateString()]);

        $this->assertFalse($dto->isSuitable($request));
    }
}
