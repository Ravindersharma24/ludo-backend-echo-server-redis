<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            AdminCommissionSeeder::class,
            CommissionLimitSeeder::class,
            DocumentSeeder::class,
            StateSeeder::class,
            GameListingsTableSeeder::class,
            BattleManagementSeeder::class,
            ActivateMannualSettingSeeder::class,
        ]);
    }
}
