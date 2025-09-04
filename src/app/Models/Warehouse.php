<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['wb_id', 'name', 'address', 'work_time', 'accepts_qr', 'active'];

    public static function getActiveWarehouses()
    {
        return self::where('active', 1)
            ->orderBy('name')
            ->get(['wb_id', 'name']);
    }

    public static function getNameByWbId(int $wbId): ?string
    {
        return self::where('wb_id', $wbId)->value('name');
    }
}
