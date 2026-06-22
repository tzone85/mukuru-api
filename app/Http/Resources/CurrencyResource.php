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
            'rate' => (float) $this->exchange_rate,
            // round() avoids binary float artefacts (e.g. 0.035 * 100 => 3.5000000000000004)
            'surcharge_percentage' => round((float) $this->surcharge_rate * 100, 4),
            'discount_percentage' => round((float) $this->discount_rate * 100, 4),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
