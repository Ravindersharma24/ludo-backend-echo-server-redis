<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [[
            'title'      => 'Admin',
            'deleted_at' => null,
        ],
            [
                'title'      => 'User',
                'deleted_at' => null,
            ]];

        Role::insert($roles);
    }
}
