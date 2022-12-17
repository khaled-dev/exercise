<?php

namespace App\Models\Concerns;

use App\Models\Ingredient;

class IngredientStockUpdateStatementBuilder
{
    /**
     * Holds the SQL statement as string.
     *
     * @var string
     */
    private string $sqlStatement;

    /**
     * Holds ids of the updated ingredients.
     *
     * @var array
     */
    private array $ingredientIds = [];

    public function __construct()
    {
        $this->sqlStatement = 'UPDATE ' . app(Ingredient::class)->getTable() . ' AS ING  SET stock = ( CASE ';
    }

    /**
     * deduct given weight from ingredient stock
     *
     * @param $ingredient_id
     * @param $weight
     * @return $this
     */
    public function deductIngredientWeight($ingredient_id, $weight): self
    {
        $this->ingredientIds[] = $ingredient_id;

        $this->sqlStatement .= " WHEN ING.id = $ingredient_id THEN (ING.stock - $weight) ";

        return $this;
    }

    /**
     * Build full SQL statement.
     *
     * @return string
     */
    public function build(): string
    {
        $this->sqlStatement .= ' END ) WHERE ING.id IN ( ' . implode(', ', $this->ingredientIds) . ' )';

        return $this->sqlStatement;
    }

}
