<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [[
            'name'           => 'Admin',
            'phone_no'           => '9900998789',
            'email'          => 'admin@admin.com',
            'password'       => '$2y$10$rV9JPPTQVDJBwjypDZ8mFeyfirX5QWND3MnKfJOQmZgAzsrZBUIDO',
            'remember_token' => null,
            'deleted_at'     => null,
            'active'     => 1,
            'balance' => 50.00,
            'referred_by' => '',
            'affiliate_id' => 'AD111'
        ]];

        User::insert($users);
    }
}
