<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'currency',
        'description',
        'symbol',
        'exchange_rate',
        'surcharge_rate',
        'discount_rate'
    ];

    protected $hidden = [
        // Timestamps are now included in API response
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'surcharge_rate' => 'float',
        'discount_rate' => 'float'
    ];
}
