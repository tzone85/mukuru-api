<?php
/**
 * Created by PhpStorm.
 * User: thandomini
 * Date: 2018/07/03
 * Time: 19:56
 */

namespace App\Repository;


use App\Model\Currency;
use Illuminate\Support\Collection;

class CurrencyRepository
{
    /**
     * @var Currency
     */
    private $model;

    public function __construct(Currency $model)
    {
        $this->model = $model;
    }

    public function findAll(): Collection
    {
        return $this->model->get();
    }

    public function findOne(int $id): Currency
    {
        return $this->model->findOrFail($id);
    }

    public function findByCurrency(string $currency): Currency
    {
        return $this->model->where('currency', $currency)->first();
    }
}