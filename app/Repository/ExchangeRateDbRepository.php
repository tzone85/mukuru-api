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
            'amount' => $convertedAmount,
            'exchange_rate' => $model->exchange_rate,
            'surcharge_rate' => $model->discount_rate ?? 0
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
            'amount' => $convertedAmount,
            'exchange_rate' => $model->exchange_rate,
            'surcharge_rate' => $model->discount_rate ?? 0
        ];
    }
}