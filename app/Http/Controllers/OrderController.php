<?php

namespace App\Http\Controllers;

use App\Repository\OrderRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var OrderRepository
     */
    private $repository;

    /**
     * OrderController constructor.
     * @param OrderRepository $repository
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json($this->repository->findAll());
    }

    /**
     * Validation is enforced by the validate.request middleware (CreateOrderRequest).
     * Only the three client-supplied fields are forwarded; all currency-derived
     * amounts are computed server-side in the repository.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $attributes = $request->only(['currency', 'foreign_currency_amount', 'total_amount']);

        try {
            $order = $this->repository->create($attributes);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        return response()->json($order, 200);
    }
}
