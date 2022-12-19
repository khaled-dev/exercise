<?php

namespace App\Services;

use App\Mail\OutOfStockNotifyMail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Services\Concerns\StockValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class IngredientService
{
    /**
     * Collection of ingredient,requested quantity.
     *
     * @var Collection
     */
    private Collection $requestedIngredientWeights;

    /**
     * stock validator service class.
     *
     * @var StockValidator
     */
    private StockValidator $stockValidator;

    public function __construct(private Request $request)
    {
        //TODO: rename
        $this->requestedIngredientWeights = Product::ingredientQuantity($this->extractProductsFromRequest())->ingredientQuantityUnique();
        $this->stockValidator              = new StockValidator($this->requestedIngredientWeights);
    }

    /**
     * check has some out of stock ingredient
     *
     * @return bool
     */
    public function isOutOfStock(): bool
    {
        return ! $this->stockValidator->validate();
    }

    /**
     * returns the out of stock ingredient name
     *
     * @return array
     */
    public function getMissingItemsNames(): array
    {
        return $this->stockValidator->getMissingItemsIdName();
    }

    /**
     * Holds the order creation logic.
     *
     * @return mixed
     */
    public function create()
    {
        $order = Order::create();

        foreach ($this->extractProductsFromRequest() as $product) {
            for ($q = $product['quantity']; $q > 0; $q--) {
                $order->products()->attach($product['product_id']);
            }
        }
        Ingredient::updateStock($this->requestedIngredientWeights);

        $this->handleStockRefillment();

        return $order;
    }

    /**
     * Request a refillment if needed
     *
     * @return void
     */
    private function handleStockRefillment()
    {
        $ingredients = Ingredient::whereIn(
            'id', $this->requestedIngredientWeights->keys()
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

    /**
     * returns products from the payload.
     *
     * @return array
     */
    private function extractProductsFromRequest(): array
    {
        return $this->request->get('products');
    }

    /**
     * returns product ids from the payload.
     *
     * @return array
     */
    private function extractProductsIdFromRequest(): array
    {
        return array_column($this->extractProductsFromRequest(), 'product_id');
    }
}
