<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchRequest extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $fillable = [
        'user_id', 'box_type_id', 'max_coefficient', 'status', 'warehouses',
        'started_at', 'stopped_at',
        'date_from',
        'date_to',
    ];

    protected $casts = [
        'warehouses' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return mixed
     */
    public static function getCurrentActiveRequests()
    {
        return self::where('status', self::STATUS_ACTIVE)
            /*->where(function ($q) {
                $q->whereNull('date_from')
                    ->orWhereDate('date_from', '<=', now()->toDateString());
            })*/
            ->where(function ($q) {
                $q->whereNull('date_to')
                    ->orWhereDate('date_to', '>=', now()->toDateString());
            })
            ->orderByDesc('id')
            ->get();
    }

    public static function getExpiredActiveRequests()
    {
        return self::where('status', self::STATUS_ACTIVE)
            ->whereNotNull('date_to')
            ->whereDate('date_to', '<', now()->toDateString())
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @return bool
     */
    public function disableRequest(): bool
    {
        return $this->update([
            'status' => self::STATUS_INACTIVE,
            'stopped_at' => now()
        ]);
    }

    public function getWarehouseNamesAttribute(): array
    {
        if (!is_array($this->warehouses) || empty($this->warehouses)) {
            return [];
        }

        return \App\Models\Warehouse::whereIn('wb_id', $this->warehouses)
            ->pluck('name', 'wb_id')
            ->toArray();
    }
}
