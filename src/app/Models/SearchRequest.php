<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'box_type_id', 'max_coefficient', 'status',
        'started_at', 'stopped_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
