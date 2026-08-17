<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Role;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
        	[
        		'name' => 'Sistemas',
        		'email' => 'admin@example.com',
        		'role_id' => Role::getIdByCode('SYS'),
        		'password' => bcrypt('sysadministrator'),
        		'active' => true
        	]
        ];

        foreach ($users as $key => $item) {
        	User::updateOrCreate(
                [
                    'email' => $item['email']
                ],
                [
                    'name' => $item['name'],
                    'role_id' => $item['role_id'],
                    'password' => $item['password'],
                    'active' => $item['active']
                ]
            );
        }
    }
}
