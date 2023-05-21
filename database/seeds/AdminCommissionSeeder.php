<?php

use Illuminate\Database\Seeder;
use App\AdminCommission;

class AdminCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminCommission = [
            [
                'commission_type'  => '1',
                'from_amount'  => 500,
                'to_amount'  => '',
                'commission_value' => 5,
                'condition'  => '1',
            ],
            [
                'commission_type'  => '1',
                'from_amount'  => 1000,
                'to_amount'  => '',
                'commission_value' => 2.5,
                'condition'  => '2',
            ],
            [
                'commission_type'  => '2',
                'from_amount'  => 500,
                'to_amount'  => 1000,
                'commission_value' => 25,
                'condition'  => '3',
            ],
        ];

        AdminCommission::insert($adminCommission);
    }
}
