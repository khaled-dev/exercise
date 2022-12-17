<?php

namespace App\Http\Services;

use App\Http\Services\Concerns\StockValidator;
use App\Models\Concerns\IngredientStockUpdateStatementBuilder;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class IngredientService
{

    private Collection $requestedIngredientQuantity;

    public function __construct(private Request $request)
    {
        $this->requestedIngredientQuantity = Product::ingredientQuantity($this->extractProductsIdFromRequest())->get();
    }

    public function verifyStock():? array
    {
        $stockValidator = new StockValidator($this->requestedIngredientQuantity);

        if ($stockValidator->validate()) {
            return null;
        }

        return $stockValidator->getMissingItemsIdName();
    }

    public function create()
    {
        $order = Order::create(['total_price' => 3]);

        foreach ($this->extractProductsFromRequest() as $product) {
            for ($q = $product['quantity']; $q > 0; $q--) {
                $order->products()->attach($product['product_id']);
            }
        }

        // update stock //
        // get products -> min weight from ingredients
        Ingredient::updateStock($this->requestedIngredientQuantity);


        // email event  //
        // check the flag
        // if false -> check the 50%
        // if less -> send an email & update the flag


    }

    private function extractProductsFromRequest(): array
    {
        return $this->request->get('products');
    }

    private function extractProductsIdFromRequest(): array
    {
        return array_column($this->extractProductsFromRequest(), 'product_id');
    }
}
