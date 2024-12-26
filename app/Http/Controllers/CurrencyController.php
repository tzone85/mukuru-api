<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetForeignCurrencyAmountRequest;
use App\Http\Requests\GetTotalAmountRequest;
use App\Http\Resources\CurrencyResource;
use App\Repository\CurrencyRepository;
use App\Service\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

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
     *      "symbol": "$",
     *      "exchange_rate": 1.0,
     *      "surcharge_rate": 0.05
     *    }
     *  ]
     * }
     * @response 200 scenario="Success" {
     *   "data": [...]
     * }
     * 
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return CurrencyResource::collection($this->repository->findAll());
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
     *     "symbol": "$",
     *     "exchange_rate": 1.0,
     *     "surcharge_rate": 0.05
     *   }
     * }
     * @response 404 scenario="Currency not found" {
     *   "error": "Currency not found"
     * }
     * 
     * @param int $id
     * @return JsonResponse|CurrencyResource
     */
    public function show($id)
    {
        $currency = $this->repository->find($id);
        
        if (!$currency) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        return new CurrencyResource($currency);
    }

    /**
     * Calculate Total Amount
     * 
     * Calculate the total amount in USD based on the provided foreign currency amount.
     * 
     * @bodyParam currency string required The currency code. Example: EUR
     * @bodyParam foreign_currency_amount numeric required The amount to convert. Example: 100.50
     * 
     * @response {
     *   "data": {
     *     "foreign_currency_amount": "100.50",
     *     "total_amount": "85.42",
     *     "exchange_rate": "0.85",
     *     "surcharge_rate": "0.05",
     *     "currency": "EUR"
     *   }
     * }
     * @response 404 scenario="Currency not found" {
     *   "error": "Currency not found"
     * }
     * @response 422 scenario="Validation Error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "currency": ["The currency field is required."],
     *     "foreign_currency_amount": ["The foreign currency amount field is required."]
     *   }
     * }
     * 
     * @param GetTotalAmountRequest $request
     * @return JsonResponse
     */
    public function getTotalAmount(GetTotalAmountRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $currency = $validatedData['currency'];
            $foreignCurrencyAmount = $validatedData['foreign_currency_amount'];

            $result = $this->service->convertToDollar($currency, $foreignCurrencyAmount);

            return response()->json([
                'data' => [
                    'foreign_currency_amount' => $foreignCurrencyAmount,
                    'total_amount' => $result['amount'],
                    'exchange_rate' => $result['exchange_rate'],
                    'surcharge_rate' => $result['surcharge_rate'],
                    'currency' => $currency
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Get Foreign Currency Amount
     * 
     * Convert a USD amount to a foreign currency using current exchange rates.
     * 
     * @bodyParam currency string required The target currency code. Example: EUR
     * @bodyParam total_amount numeric required The USD amount to convert. Example: 100.50
     * 
     * @response {
     *   "data": {
     *     "foreign_currency_amount": "85.42",
     *     "total_amount": "100.50",
     *     "exchange_rate": "0.85",
     *     "surcharge_rate": "0.05",
     *     "currency": "EUR"
     *   }
     * }
     * @response 404 scenario="Currency not found" {
     *   "error": "Currency not found"
     * }
     * @response 422 scenario="Validation Error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "currency": ["The currency field is required."],
     *     "total_amount": ["The total amount field is required."]
     *   }
     * }
     * 
     * @param GetForeignCurrencyAmountRequest $request
     * @return JsonResponse
     */
    public function getForeignCurrencyAmount(GetForeignCurrencyAmountRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $currency = $validatedData['currency'];
            $totalAmount = $validatedData['total_amount'];

            $result = $this->service->convertToForeign($currency, $totalAmount);

            return response()->json([
                'data' => [
                    'foreign_currency_amount' => $result['amount'],
                    'total_amount' => $totalAmount,
                    'exchange_rate' => $result['exchange_rate'],
                    'surcharge_rate' => $result['surcharge_rate'],
                    'currency' => $currency
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}