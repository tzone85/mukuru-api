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
        'surcharge_rate'
    ];

    protected $hidden =[
      'created_at','updated_at'
    ];
}