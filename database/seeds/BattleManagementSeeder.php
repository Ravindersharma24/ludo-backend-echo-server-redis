<?php

use Illuminate\Database\Seeder;
use App\BattleManagement;

class BattleManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $battle_management = [
            [
                'minimum_amount'  => 50,
                'maximum_battle'  => 2,
            ],
        ];

        BattleManagement::insert($battle_management);
    }
}
