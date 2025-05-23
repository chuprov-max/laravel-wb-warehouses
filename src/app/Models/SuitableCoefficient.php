<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuitableCoefficient extends Model
{
    use HasFactory;
    
    const STATUS_CREATED = 1;
    const STATUS_APPLIED = 2;
    const STATUS_FAILED = 3;

    protected $fillable = ['warehouse_id', 'coefficient', 'allow_unload', 'box_type_id', 'accept_date', 'status'];

}
