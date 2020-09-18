<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WarehouseSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('warehouse_systems')->insert([[
            'name' => 'WAREHOUSE_X',
            'get_endpoint_url' => 'https://5f591c568040620016ab8de2.mockapi.io/api/v1/warehouse-items',
        ],
        // [
        //     'name' => 'WAREHOUSE_Z',
        //     'get_endpoint_url' => 'https://5f591c568040620016ab8de2.mockapi.io/api/v1/warehouse-items',
        // ]
        ]);
    }
}
