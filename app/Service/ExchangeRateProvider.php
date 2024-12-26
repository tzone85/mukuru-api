<?php

namespace App\Service;

use App\Model\Currency;
use App\Repository\CurrencyRepository;

class ExchangeRateProvider implements ExchangeRateInterface
{
    private $repository;

    public function __construct(CurrencyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function convert(int $amount, string $currency): Currency
    {
        $currencyModel = $this->repository->findByCurrency($currency);
        if (!$currencyModel) {
            throw new \InvalidArgumentException("Currency not found: {$currency}");
        }
        return $currencyModel;
    }

    public function convertToDollar(string $currency, float $amount)
    {
        $currencyModel = $this->repository->findByCurrency($currency);
        if (!$currencyModel) {
            throw new \InvalidArgumentException("Currency not found: {$currency}");
        }

        $exchangeRate = $currencyModel->exchange_rate;
        $surchargeRate = $currencyModel->surcharge_rate;
        
        $convertedAmount = $amount * $exchangeRate;
        $surcharge = $convertedAmount * $surchargeRate;
        $totalAmount = $convertedAmount + $surcharge;

        return [
            'amount' => $totalAmount,
            'exchange_rate' => $exchangeRate,
            'surcharge_rate' => $surchargeRate
        ];
    }

    public function convertToForeign(string $currency, float $amount)
    {
        $currencyModel = $this->repository->findByCurrency($currency);
        if (!$currencyModel) {
            throw new \InvalidArgumentException("Currency not found: {$currency}");
        }

        $exchangeRate = $currencyModel->exchange_rate;
        $surchargeRate = $currencyModel->surcharge_rate;
        
        $surcharge = $amount * $surchargeRate;
        $totalWithSurcharge = $amount + $surcharge;
        $foreignAmount = $totalWithSurcharge / $exchangeRate;

        return [
            'amount' => $foreignAmount,
            'exchange_rate' => $exchangeRate,
            'surcharge_rate' => $surchargeRate
        ];
    }
}
