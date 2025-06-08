<?php

namespace App\Helpers;

class WarehouseHelper
{
    public static function getNameById(int $id): ?string
    {
        return collect(config('warehouses.acceptancePriority'))
                ->firstWhere('id', $id)['name'] ?? null;
    }

    public static function getIdByName(string $name): ?int
    {
        return collect(config('warehouses.acceptancePriority'))
                ->firstWhere('name', $name)['id'] ?? null;
    }
}
