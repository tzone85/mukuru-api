<?php

namespace App\Service;

use App\Model\Currency;

/**
 * Interface ExchangeRateInterface
 * @package App\Service
 */
interface ExchangeRateInterface
{
    /**
     * @param int $amount
     * @param string $currency
     * @return Currency
     */
    public function convert(int $amount, string $currency): Currency;

    /**
     * @param string $currency
     * @param float $amount
     * @return mixed
     */
    public function convertToDollar(string $currency, float $amount);

    /**
     * @param string $currency
     * @param float $amount
     * @return mixed
     */
    public function convertToForeign(string $currency, float $amount);
}