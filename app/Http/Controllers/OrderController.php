<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Services\IngredientService;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * List all Orders.
     *
     * @return OrderCollection
     */
    public function index(): OrderCollection
    {
        return new OrderCollection(Order::all());
    }

    public function store(StoreOrderRequest $request): OrderResource
    {
        $ingredientService = new IngredientService($request);

        if ($ingredientService->verifyStock() !== null) {
            // TODO: handle error message
        }

        return new OrderResource(
            $ingredientService->create()
        );
    }
}
