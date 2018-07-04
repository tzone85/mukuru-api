<?php

namespace App\Http\Controllers;

use App\Repository\CurrencyRepository;
use App\Service\ExchangeRateService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CurrencyController
 * @package App\Http\Controllers
 */
class CurrencyController extends Controller
{
    /**
     * @var CurrencyRepository
     */
    private $repository;
    /**
     * @var ExchangeRateService
     */
    private $service;

    /**
     * CurrencyController constructor.
     * @param CurrencyRepository $repository
     * @param ExchangeRateService $service
     */
    public function __construct(CurrencyRepository $repository, ExchangeRateService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function index()
    {
        return $this->repository->findAll();
    }

    /**
     * @param int $id
     * @return \App\Model\Currency
     */
    public function show(int $id)
    {
        return $this->repository->findOne($id);
    }

    /**
     * @param FormRequest $request
     * @return mixed
     */
    public function getTotalAmount(FormRequest $request)
    {
        return $this->service->convertToDollar($request->get('currency'), $request->get('foreign_currency_amount'));
    }

    /**
     * @param FormRequest $request
     * @return mixed
     */
    public function getForeignCurrencyAmount(FormRequest $request)
    {
        return $this->service->convertToForeign($request->get('currency'), $request->get('total_amount'));
    }
}