<?php

namespace Tests\Feature;


use App\Mail\OutOfStockNotifyMail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Json\OrdersJsonResponse;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use OrdersJsonResponse;


    public function test_list_orders_successful_response()
    {
        $response = $this->get('/api/orders');
        $response->assertStatus(200);
    }

    public function test_list_orders_entities_successfully()
    {
        Order::factory(5)->has(Product::factory()->count(3))->create();
        $orders = Order::all();

        $this->getJson(route('orders.list'))
            ->assertOk()
            ->assertJsonStructure($this->listJsonResponse())
            ->assertJson(function (AssertableJson $json) use ($orders) {
                return $json->has('data')
                    ->has('data.orders', $orders->count())
                    ->has('data.orders.0', function ($json) use ($orders) {
                        return $json->where('id', $orders->first()->id)
                            ->where('products.0.id', $orders->first()->products()->first()->id)
                            ->etc();
                    });
            });
    }

    // success
    public function test_create_order_successfully()
    {
        Mail::fake();

        (new DatabaseSeeder())->run();

        $this->postJson(route('orders.list'), [
                'products' => [
                    [
                        'product_id' => 2,
                        'quantity'   => 2,
                    ],
                    [
                        'product_id' => 1,
                        'quantity'   => 3,
                    ],
                ]
            ])
            ->assertCreated()
            ->assertJsonStructure($this->createJsonResponse())
            ->assertJson(function (AssertableJson $json)  {
                return $json->has('data')
                    ->has('data.products', 5)
                    ->has('data.products.0', function ($json) {
                        return $json->where('id', 2)->etc();
                    });
            });

        // no email sent
        Mail::assertNotSent(OutOfStockNotifyMail::class);
    }

    // validation
    public function test_create_order_validation_errors()
    {
        (new DatabaseSeeder())->run();

        $this->postJson(route('orders.list'), [
            'products' => [
                [
                    'product_id' => 'invalid',
                    'quantity'   => 'invalid',
                ],
                [
                    'product_id' => 'invalid',
                    'quantity'   => 'invalid',
                ]
            ]
        ])
            ->assertUnprocessable()
            ->assertJsonFragment($this->validationErrorJsonResponse());
    }

    // out of stock
    public function test_create_order_out_of_stock_error()
    {
        $beef = Ingredient::factory()->create([
            'name'          => 'Beef',
            'stock'         => 150,
            'maximum_stock' => 150,
        ]);

        $beefBurger = Product::factory()
            ->create(['name'  => 'Beef Burger', 'price' => 100]);
        $beefBurger->recipes()->create([
            'ingredient_id' => $beef->id,
            'weight'        => 150,
        ]);

        $this->postJson(route('orders.list'), [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity'   => 2,
                ],
            ]
        ])
            ->assertUnprocessable()
            ->assertJsonFragment($this->outOfStockErrorJsonResponse());
    }

    // email sent
    public function test_create_order_send_email()
    {
        Mail::fake();

        $beef = Ingredient::factory()->create([
            'name'          => 'Beef',
            'stock'         => 200,
            'maximum_stock' => 200,
        ]);

        $beefBurger = Product::factory()
            ->create(['name'  => 'Beef Burger', 'price' => 100]);
        $beefBurger->recipes()->create([
            'ingredient_id' => $beef->id,
            'weight'        => 150,
        ]);

        $this->postJson(route('orders.list'), [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity'   => 1,
                ],
            ]
        ])->assertCreated();

        // email sent
        Mail::assertSent(OutOfStockNotifyMail::class);
    }

}
