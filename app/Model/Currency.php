<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'symbol',
        'description',
        'currency',
        'exchange_rate',
        'surcharge_rate',
        'discount_rate'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'surcharge_rate' => 'float',
        'discount_rate' => 'float'
    ];
}
