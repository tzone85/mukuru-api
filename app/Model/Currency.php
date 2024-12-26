<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

// class Currency extends Model
class Currency extends JsonResource
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

    protected $appends = [
        'discountRate'
    ];

    public function getDiscountRateAttribute()
    {
        return $this->discount_rate ?? 0;
    }
}
