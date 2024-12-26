<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->currency,
            'name' => $this->description,
            'symbol' => $this->symbol,
            'exchange_rate' => (float) $this->exchange_rate,
            'surcharge_rate' => (float) $this->surcharge_rate,
            'discount_rate' => (float) $this->discount_rate,
        ];
    }
}
