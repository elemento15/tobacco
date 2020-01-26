<?php

use Illuminate\Database\Seeder;
use App\Warehouse;

class WarehousesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $warehouses = [
            ['name' => 'BODEGA PRINCIPAL'],
            ['name' => 'BODEGA SECUNDARIA'],
        ];


        if (! Warehouse::count()) {
            foreach ($warehouses as $key => $item) {
                Warehouse::create($item);
            }
        }
    }
}
