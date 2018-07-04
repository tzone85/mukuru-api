<?php

namespace App\Http\Controllers;

use App\Repository\OrderRepository;
use Illuminate\Foundation\Http\FormRequest;

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
     * @param FormRequest $request
     * @return \App\Model\Order
     */
    public function store(FormRequest $request)
    {
        return $this->repository->create($request->all());
    }
}
