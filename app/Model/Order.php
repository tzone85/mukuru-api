<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'currency',
        'exchange_rate',
        'surcharge_rate',
        'foreign_currency_amount',
        'total_amount',
        'surcharge_amount',
        'discount_amount',
        'discount_rate'
    ];
}