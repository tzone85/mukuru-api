<?php

namespace App\Http\Controllers;

use App\Repository\CurrencyRepository;
use App\Service\ExchangeRateService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

/**
 * @group Currency Management
 * 
 * APIs for managing currencies and exchange rates
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
     * List all currencies
     * 
     * Returns a list of all available currencies in the system.
     * 
     * @response {
     *  "data": [
     *    {
     *      "id": 1,
     *      "code": "USD",
     *      "name": "US Dollar",
     *      "symbol": "$"
     *    }
     *  ]
     * }
     * @response 200 scenario="Success" {
     *   "data": [...]
     * }
     * 
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json($this->repository->findAll());
    }

    /**
     * Get Currency Details
     * 
     * Retrieve detailed information about a specific currency.
     *
     * @urlParam id integer required The ID of the currency. Example: 1
     * 
     * @response {
     *   "data": {
     *     "id": 1,
     *     "code": "USD",
     *     "name": "US Dollar",
     *     "symbol": "$"
     *   }
     * }
     * @response 404 scenario="Currency not found" {
     *   "error": "Currency not found"
     * }
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $currency = $this->repository->find($id);
        
        if (!$currency) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        return response()->json($currency);
    }

    /**
     * Calculate Total Amount
     * 
     * Calculate the total amount in the target currency based on the provided parameters.
     *
     * @bodyParam currency string required The currency code. Example: USD
     * @bodyParam foreign_currency_amount numeric required The amount to convert. Example: 100.50
     * 
     * @response {
     *   "data": {
     *     "foreign_currency_amount": "100.50",
     *     "total_amount": "85.42",
     *     "exchange_rate": "0.85",
     *     "surcharge_rate": "0.05",
     *     "currency": "USD"
     *   }
     * }
     * @response 422 scenario="Validation Error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "currency": ["The currency field is required."]
     *   }
     * }
     * 
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
     * Get Foreign Currency Amount
     * 
     * Convert an amount from one currency to another using current exchange rates.
     *
     * @bodyParam currency string required The currency code. Example: USD
     * @bodyParam total_amount numeric required The amount to convert. Example: 100.50
     * 
     * @response {
     *   "data": {
     *     "foreign_currency_amount": "85.42",
     *     "total_amount": "100.50",
     *     "exchange_rate": "0.85",
     *     "surcharge_rate": "0.05",
     *     "currency": "USD"
     *   }
     * }
     * @response 422 scenario="Validation Error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "currency": ["The currency field is required."]
     *   }
     * }
     * 
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