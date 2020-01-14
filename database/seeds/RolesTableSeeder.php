<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
        	['name' => 'Sistemas', 'code' => 'SYS'],
        	['name' => 'Administrador', 'code' => 'ADM'],
            ['name' => 'Encargado de Inventarios', 'code' => 'INV'],
            ['name' => 'Almacenista', 'code' => 'ALM'],
            ['name' => 'Auxiliar', 'code' => 'AUX'],
        ];


        if (! Role::count()) {
            foreach ($roles as $key => $item) {
            	Role::create($item);
            }
        }
    }
}
