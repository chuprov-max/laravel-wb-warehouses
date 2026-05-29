<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuitableCoefficient extends Model
{
    use HasFactory;

    const STATUS_CREATED = 1;
    const STATUS_NOTIFIED = 2;
    const STATUS_FAILED = 3;

    const BOX_TYPE_ID_KOROBA = 2;
    const BOX_TYPE_ID_MONOPALLETTY = 5;
    const BOX_TYPE_ID_SUPERSAFE = 6;

    public static $boxTypesRussianNames = [
        self::BOX_TYPE_ID_KOROBA =>  'Короба',
        self::BOX_TYPE_ID_MONOPALLETTY => 'Монопаллеты',
        self::BOX_TYPE_ID_SUPERSAFE => 'Суперсейф'
    ];

    protected $fillable = ['warehouse_id', 'search_request_id', 'coefficient', 'allow_unload', 'box_type_id', 'accept_date', 'status'];

    public function getBoxTypeRussianName(): string
    {
        return self::$boxTypesRussianNames[$this->box_type_id] ?? '';
    }

    public static function getBoxTypeRussianNameById(int $boxTypeId): string
    {
        return self::$boxTypesRussianNames[$boxTypeId] ?? '';
    }
}
