<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [[
            'title'      => 'user_management_access',
        ],
            [
                'title'      => 'permission_create',
            ],
            [
                'title'      => 'permission_edit',
            ],
            [
                'title'      => 'permission_show',
            ],
            [
                'title'      => 'permission_delete',
            ],
            [
                'title'      => 'permission_access',
            ],
            [
                'title'      => 'role_create',
            ],
            [
                'title'      => 'role_edit',
            ],
            [
                'title'      => 'role_show',
            ],
            [
                'title'      => 'role_delete',
            ],
            [
                'title'      => 'role_access',
            ],
            [
                'title'      => 'user_create',
            ],
            [
                'title'      => 'user_edit',
            ],
            [
                'title'      => 'user_show',
            ],
            [
                'title'      => 'user_delete',
            ],
            [
                'title'      => 'user_access',
            ],
            [
                'title'      => 'product_create',
            ],
            [
                'title'      => 'product_edit',
            ],
            [
                'title'      => 'product_show',
            ],
            [
                'title'      => 'product_delete',
            ],
            [
                'title'      => 'product_access',
            ]];

        Permission::insert($permissions);
    }
}
