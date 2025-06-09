<?php

namespace App\Models;

use App\Helpers\WarehouseHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class SuitableCoefficient extends Model
{
    use HasFactory;

    const STATUS_CREATED = 1;
    const STATUS_NOTIFIED = 2;
    const STATUS_FAILED = 3;

    const BOX_TYPE_ID_KOROBA = 2;
    const BOX_TYPE_ID_MONOPALLETTY = 5;
    const BOX_TYPE_ID_SUPERSAFE = 6;

    protected $fillable = ['warehouse_id', 'coefficient', 'allow_unload', 'box_type_id', 'accept_date', 'status'];

    public function getBoxTypeRussianName(): string
    {
        if ($this->box_type_id == self::BOX_TYPE_ID_KOROBA) {
            return 'Короба';
        } elseif ($this->box_type_id == self::BOX_TYPE_ID_MONOPALLETTY) {
            return 'Монопаллеты';
        }
        return "Суперсейф";
    }
}
