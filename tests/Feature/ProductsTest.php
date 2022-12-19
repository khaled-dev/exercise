<?php

namespace Tests\Feature;


use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Json\ProductsJsonResponse;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use ProductsJsonResponse;


    public function test_list_products_successful_response()
    {
        $response = $this->get('/api/products');
        $response->assertStatus(200);
    }

    public function test_list_products_entities_successfully()
    {
        Product::factory(5)->create();
        $products = Product::all();

        $this->getJson(route('products.list'))
            ->assertOk()
            ->assertJsonStructure($this->listJsonResponse())
            ->assertJson(function (AssertableJson $json) use ($products) {
                return $json->has('data')
                    ->has('data.products', $products->count())
                    ->has('data.products.0', function ($json) use ($products) {
                        return $json->where('id', $products->first()->id)
                            ->where('name', $products->first()->name)
                            ->where('price', $products->first()->converted_price)
                            ->etc();
                    });
            });
    }


}
