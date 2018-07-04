<?php

namespace App\Service;

use App\Model\Currency;

/**
 * Class ExchangeRateService
 */
class ExchangeRateService
{
    /**
     * @var ExchangeRateInterface
     */
    private $provider;

    public function __construct(ExchangeRateInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param int $amount
     * @param string $currency
     * @return Currency
     */
    public function convert(int $amount, string $currency): Currency
    {
        return $this->provider->convert($amount, $currency);
    }

    /**
     * @param string $currency
     * @param int $amount
     * @return mixed
     */
    public function convertToDollar(string $currency, float $amount)
    {
        return $this->provider->convertToDollar($currency, $amount);
    }

    /**
     * @param string $currency
     * @param int $amount
     * @return mixed
     */
    public function convertToForeign(string $currency, float $amount)
    {
        return $this->provider->convertToForeign($currency, $amount);
    }
}