<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Services\IngredientService;
use App\Models\Order;
use Illuminate\Http\Request;

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

    public function store(Request $request): OrderResource
    {
        // TODO: move it
        $request->validate([
            'products'              => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|integer|max_digits:1000', //TODO: more than 0
        ]);

        $ingredientService = new IngredientService($request);

        if ($ingredientService->verifyStock() !== null) {
            // TODO: handle error message
        }

        $ingredientService->create();


        return new OrderResource(Order::all()->first());
    }
}
