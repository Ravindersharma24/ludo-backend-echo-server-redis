<?php

use Illuminate\Database\Seeder;
use App\CommissionLimitManagement;

class CommissionLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $commission_limit = [
            [
                'refer_commission_percentage'  => 1,
                'wallet_withdraw_limit'  => 95,
                'refer_reedem_limit'  => 55,
                'max_refer_commission'  => 10000,
                'pending_game_penalty_amt'  => 25,
                'wrong_result_penalty_amt'  => 50,
            ],
        ];

        CommissionLimitManagement::insert($commission_limit);
    }
}
