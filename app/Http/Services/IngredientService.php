<?php

namespace App\Http\Services;

use App\Http\Services\Concerns\StockValidator;
use App\Mail\OutOfStockNotifyMail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $order = Order::create();

        foreach ($this->extractProductsFromRequest() as $product) {
            for ($q = $product['quantity']; $q > 0; $q--) {
                $order->products()->attach($product['product_id']);
            }
        }

        Ingredient::updateStock($this->requestedIngredientQuantity);

        $this->handleStockRefillment();

        return $order;
    }

    private function handleStockRefillment()
    {
        $ingredients = Ingredient::whereIn(
            'id',$this->requestedIngredientQuantity->pluck('id')
        )->get();

        foreach ($ingredients as $ingredient) {
            if ($ingredient->our_of_stock_notification) {
                continue;
            }

            if ($ingredient->stock_below_safe_point) {
                // send email
                Mail::to('refillment@guy.com')->send(new OutOfStockNotifyMail($ingredient));

                // update the flag
                $ingredient->update(['our_of_stock_notification' => 1]);
            }
        }
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
