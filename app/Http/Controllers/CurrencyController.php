<?php

namespace App\Http\Controllers;

use App\Repository\CurrencyRepository;
use App\Service\ExchangeRateService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

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
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json($this->repository->findAll());
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $currency = $this->repository->findOne($id);
        if (!$currency) {
            return response()->json(['message' => 'Currency not found'], 404);
        }
        return response()->json($currency);
    }

    /**
     * @param FormRequest $request
     * @return JsonResponse
     */
    public function getTotalAmount(FormRequest $request)
    {
        $currency = $request->get('currency');
        $amount = $request->get('foreign_currency_amount');

        if (!$this->repository->findByCurrency($currency)) {
            return response()->json(['message' => 'Currency not found'], 404);
        }

        $result = $this->service->convertToDollar($currency, $amount);
        return response()->json([
            'foreign_currency_amount' => $amount,
            'total_amount' => $result['amount'],
            'exchange_rate' => $result['exchange_rate'],
            'surcharge_rate' => $result['surcharge_rate'],
            'currency' => $currency
        ]);
    }

    /**
     * @param FormRequest $request
     * @return JsonResponse
     */
    public function getForeignCurrencyAmount(FormRequest $request)
    {
        $currency = $request->get('currency');
        $amount = $request->get('total_amount');

        if (!$this->repository->findByCurrency($currency)) {
            return response()->json(['message' => 'Currency not found'], 404);
        }

        $result = $this->service->convertToForeign($currency, $amount);
        return response()->json([
            'foreign_currency_amount' => $result['amount'],
            'total_amount' => $amount,
            'exchange_rate' => $result['exchange_rate'],
            'surcharge_rate' => $result['surcharge_rate'],
            'currency' => $currency
        ]);
    }
}