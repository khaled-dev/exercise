<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // create ingredients
        // products
        // orders

        $beef = Ingredient::factory()->create([
            'name' => 'Beef',
            'stock' => 20000,
        ]);
        $chicken = Ingredient::factory()->create([
            'name' => 'Chicken',
            'stock' => 20000,
        ]);
        $cheese = Ingredient::factory()->create([
            'name' => 'Cheese',
            'stock' => 5000,
        ]);
        $onion = Ingredient::factory()->create([
            'name' => 'Onion',
            'stock' => 1000,
        ]);


        $beefBurger = Product::factory()
            ->create(['name'  => 'Beef Burger', 'price' => 100]);
        $beefBurger->recipes()->createMany([
            [
                'ingredient_id' => $beef->id,
                'weight' => 150,
            ], [
                'ingredient_id' => $cheese->id,
                'weight' => 30,
            ], [
                'ingredient_id' => $onion->id,
                'weight' => 20,
            ],
        ]);

        $chickenBurger = Product::factory()
            ->create(['name'  => 'Chicken Burger', 'price' => 90]);
        $chickenBurger->recipes()->createMany([
            [
                'ingredient_id' => $chicken->id,
                'weight' => 150,
            ], [
                'ingredient_id' => $cheese->id,
                'weight' => 30,
            ], [
                'ingredient_id' => $onion->id,
                'weight' => 20,
            ],
        ]);

        Order::factory()->create()->products()->attach($chickenBurger);

    }
}
