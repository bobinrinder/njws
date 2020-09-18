<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class HandleTaskTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHandleTaskTest()
    {
        Http::fake(
            [
            '5f591c568040620016ab8de2.mockapi.io/api/v1/orders' =>
            Http::response(File::get(base_path('tests/stubs/get-orders.json')), 200, ['Headers']),
        ],
            [
            '5f591c568040620016ab8de2.mockapi.io/api/v1/warehouse-items' =>
            Http::response(File::get(base_path('tests/stubs/get-items.json')), 200, ['Headers']),
        ],
            [
            '*' => Http::response('Success', 200, ['Headers']),
        ]
        );

        $response = $this->get('/api/tasks/handle');
        $response->assertStatus(200);

        $this->assertDatabaseCount('orders', 69);
        $this->assertDatabaseCount('order_items', 81);
        $this->assertDatabaseCount('items', 87);

        $this->assertDatabaseHas('orders', [
            'id' => 1,
            'status' => 'processed'
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => 68,
            'status' => 'processed'
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => 69,
            'status' => 'failed'
        ]);

        $this->assertDatabaseHas('order_items', [
            'id' => 8,
            'is_valid' => 1
        ]);
        $this->assertDatabaseHas('order_items', [
            'id' => 9,
            'is_valid' => 0
        ]);

        $this->assertDatabaseHas('items', [
            'id' => 15,
            'sku' => 'GL5407461080790384'
        ]);
        $this->assertDatabaseHas('items', [
            'id' => 87,
            'upc' => 'TN8129581037030100043005'
        ]);
    }
}
