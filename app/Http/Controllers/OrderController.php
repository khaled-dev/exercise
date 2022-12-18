<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Services\IngredientService;
use App\Models\Order;
use \Illuminate\Http\Response;

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

    public function store(StoreOrderRequest $request): OrderResource|Response
    {
        $ingredientService = new IngredientService($request);

        if ($ingredientService->isOutOfStock()) {
            return $this->invalidInputResponse(
                '[ '
                . implode(', ', $ingredientService->getMissingItemsNames())
                . ' ] out of stock.',
            );
        }

        return new OrderResource(
            $ingredientService->create()
        );
    }
}
