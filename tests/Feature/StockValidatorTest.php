<?php

namespace Tests\Feature;

use App\DataTransferObjects\RecipeWeightDTO;
use App\Models\Ingredient;
use App\Services\Concerns\StockValidator;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class StockValidatorTest extends TestCase
{

    public function test_stock_is_valid()
    {
        // create ingredients
        $beef = Ingredient::factory()->create([
            'name'          => 'Beef',
            'stock'         => 200,
            'maximum_stock' => 200,
        ]);
        $chicken = Ingredient::factory()->create([
            'name'          => 'Chicken',
            'stock'         => 200,
            'maximum_stock' => 200,
        ]);


        // id, name, wights
        $ingredientWeights = new Collection([
            new RecipeWeightDTO(
                $beef->id,
                $beef->name,
                20,
            ),
            new RecipeWeightDTO(
                $chicken->id,
                $chicken->name,
                20,
            ),
        ]);

        $validator = new StockValidator($ingredientWeights);

        $this->assertTrue($validator->validate());

        $this->assertEquals([], $validator->getMissingItemsIdName());
    }

    public function test_stock_is_invalid()
    {
        // create ingredients
        $beef = Ingredient::factory()->create([
            'name'          => 'Beef',
            'stock'         => 200,
            'maximum_stock' => 200,
        ]);
        $chicken = Ingredient::factory()->create([
            'name'          => 'Chicken',
            'stock'         => 200,
            'maximum_stock' => 200,
        ]);


        // id, name, wights
        $ingredientWeights = new Collection([
            new RecipeWeightDTO(
                $beef->id,
                $beef->name,
                201,
            ),
            new RecipeWeightDTO(
                $chicken->id,
                $chicken->name,
                20,
            ),
        ]);

        $validator = new StockValidator($ingredientWeights);

        $this->assertFalse($validator->validate());

        $this->assertEquals([1 => 'Beef'], $validator->getMissingItemsIdName());
    }
}
