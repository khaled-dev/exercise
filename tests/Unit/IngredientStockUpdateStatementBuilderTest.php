<?php

namespace Tests\Unit;

use App\Models\Concerns\IngredientStockUpdateStatementBuilder;
use PHPUnit\Framework\TestCase;

class IngredientStockUpdateStatementBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function test_building_stock_update_statement()
    {
        $statementBuilder = (new IngredientStockUpdateStatementBuilder());

        $ingredients = [
            ['id' => 1, 'weight' => 10],
            ['id' => 2, 'weight' => 20],
            ['id' => 3, 'weight' => 30],
        ];

        foreach ($ingredients as $ingredient) {
            $statementBuilder->deductIngredientWeight($ingredient['id'], $ingredient['weight']);
        }

        $this->assertEquals($statementBuilder->build(), $this->mockedStatement());
    }

    private function mockedStatement(): string
    {
        return 'UPDATE ingredients AS ING  SET stock = ( CASE  WHEN ING.id = 1 THEN (ING.stock - 10)  WHEN ING.id = 2 THEN (ING.stock - 20)  WHEN ING.id = 3 THEN (ING.stock - 30)  END ) WHERE ING.id IN ( 1, 2, 3 )';
    }
}
