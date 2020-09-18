<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OrderSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('order_systems')->insert([
            'name' => 'ORDERING_Y',
            'get_endpoint_url' => 'https://5f591c568040620016ab8de2.mockapi.io/api/v1/orders',
            'put_endpoint_url' => 'https://5f591c568040620016ab8de2.mockapi.io/api/v1/orders/:orderId',
        ]);
    }
}
