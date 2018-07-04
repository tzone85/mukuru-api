<?php

namespace App\Repository;

use App\Model\Currency;
use App\Model\Order;
use Illuminate\Support\Collection;

class OrderRepository
{
    /**
     * @var Order
     */
    private $model;

    /**
     * OrderRepository constructor.
     * @param Order $model
     */
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model->get();
    }

    /**
     * @param int $id
     * @return Order
     */
    public function findOne(int $id): Order
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param string $currency
     * @return Order
     */
    public function findByCurrency(string $currency): Order
    {
        return $this->model->where('currency', $currency)->first();
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        $currencyModel = Currency::where('currency', array_get($attributes, 'currency'))->firstOrFail();
        $totalAmount = array_get($attributes, 'total_amount');
        $surchargeAmount = $totalAmount * $currencyModel->surcharge_rate;
        $totalAmount = $totalAmount-$surchargeAmount;

        $discountAmount = $currencyModel->discountRate * $totalAmount;
        $totalAmount = $totalAmount - $discountAmount;

        return $this->model->create([
            'currency' => $currencyModel->currency,
            'exchange_rate' => $currencyModel->exchange_rate,
            'surcharge_rate' => $currencyModel->surcharge_rate,
            'foreign_currency_amount' => array_get($attributes, 'foreign_currency_amount'),
            'total_amount' => $totalAmount,
            'surcharge_amount' => $surchargeAmount,
            'discount_amount' => $discountAmount,
            'discount_rate' => $currencyModel->discountRate
        ]);
    }
}