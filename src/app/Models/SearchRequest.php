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
        'user_id', 'box_type_id', 'max_coefficient', 'status',
        'started_at', 'stopped_at',
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
            ->orderByDesc('id')
            ->get();
    }

    // TODO call only when it will disable by schedule
    public function disableOtherRequests()
    {
        return self::where('status', self::STATUS_ACTIVE)
            ->where('id', '!=', $this->id)
            ->update(['status' => self::STATUS_INACTIVE, 'stopped_at' => now()]);
    }
}
