<?php

namespace App\Repository;

use App\Model\Currency;
use App\Service\ExchangeRateInterface;

/**
 * Class ExchangeRateDbRepository
 * @package App\Repository
 */
class ExchangeRateDbRepository implements ExchangeRateInterface
{

    /**
     * @var CurrencyRepository
     */
    private $repository;

    public function __construct(CurrencyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $amount
     * @param string $currency
     * @return Currency
     */
    public function convert(int $amount, string $currency): Currency
    {
        // TODO: Implement convert() method.
    }

    /**
     * @param string $currency
     * @param float $amount
     * @return array|mixed
     */
    public function convertToDollar(string $currency, float $amount)
    {
        $model = $this->repository->findByCurrency($currency);
        $convertedAmount = $amount * $model->exchange_rate;

        return [
            'foreign_currency_amount' => $amount,
            'total_amount' => $convertedAmount,
            'currency' => $currency
        ];
    }

    /**
     * @param string $currency
     * @param float $amount
     * @return array|mixed
     */
    public function convertToForeign(string $currency, float $amount)
    {
        $model = $this->repository->findByCurrency($currency);
        $convertedAmount = $amount / $model->exchange_rate;

        return [
            'foreign_currency_amount' => $convertedAmount,
            'total_amount' => $amount,
            'currency' => $currency,
            'model' => $model
        ];
    }
}