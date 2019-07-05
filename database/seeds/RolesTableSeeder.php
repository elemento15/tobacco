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
        	['name' => 'Administrador', 'code' => 'ADM'],
        	['name' => 'Sistemas', 'code' => 'SYS']
        ];


        if (! Role::count()) {
            foreach ($roles as $key => $item) {
            	Role::create($item);
            }
        }
    }
}
