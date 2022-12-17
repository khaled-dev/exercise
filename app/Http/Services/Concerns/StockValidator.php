<?php

namespace App\Http\Services\Concerns;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class StockValidator
{

    /**
     * Holds Eloquent\Builder query.
     *
     * @var Builder
     */
    private Builder $query;

    /**
     * Holds Eloquent\Builder query results.
     *
     * @var Collection
     */
    private Collection $queryResult;

    public function __construct(private Collection $ingredientWeights)
    {
        $this->query = Ingredient::query();
    }

    /**
     * Validate all ingredient ar in stock.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $this->ingredientWeights->each(function ($ingredientWeight)  {
            $this->addIngredientWeight($ingredientWeight->id, $ingredientWeight->weights);
        });

        $this->queryResult = $this->query->get();

        return $this->queryResult->count() === $this->ingredientWeights->count();
    }

    /**
     * Returns [id, name] of out of stock ingredients.
     *
     * @return array
     */
    public function getMissingItemsIdName(): array
    {
        $missingItems = [];

        foreach ($this->ingredientWeights as $productWeight) {
            if ($this->queryResult->find($productWeight->id) === null) {
                $missingItems[$productWeight->id] = $productWeight->name;
            }
        }

        return $missingItems;
    }

    /**
     * Append conditions to Eloquent\Builder query.
     *
     * @param int $ingredient_id
     * @param int $weight
     * @return void
     */
    private function addIngredientWeight(int $ingredient_id, int $weight): void
    {
        $this->query->orWhere(function ($q) use ($ingredient_id, $weight) {
            $q->where('id', $ingredient_id)->where('stock', '>=', $weight);
        });
    }
}
