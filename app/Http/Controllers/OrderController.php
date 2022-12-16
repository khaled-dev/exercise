<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
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

    public function store(): OrderResource
    {
        // validate inputs
        // validate stock
        // orderService (orderBuilder, sendEmail[after-creation], update stock)


        return new OrderResource();
    }
}
