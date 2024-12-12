<?php

namespace App\Http\Controllers;

use App\Repository\OrderRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

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
     * @param FormRequest $request
     * @return JsonResponse
     */
    public function store(FormRequest $request)
    {
        $order = $this->repository->create($request->all());
        return response()->json($order, 200);
    }
}
