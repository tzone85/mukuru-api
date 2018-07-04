<?php

namespace App\Repository;

use App\Model\Currency;
use App\Service\ExchangeRateInterface;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class JsonRatesRepository
 * @package App\Repository
 */
class JsonRatesRepository implements ExchangeRateInterface
{

    /**
     * @param int $amount
     * @param string $currency
     * @return Currency
     */
    public function convert(int $amount, string $currency): Currency
    {
        // TODO: Implement convert() method.
    }

    public function convertToDollar(FormRequest $request)
    {
        // TODO: Implement convertToDollar() method.
    }

    public function convertToForeign(FormRequest $request)
    {
        // TODO: Implement convertToForeign() method.
    }
}