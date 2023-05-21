<?php

use Illuminate\Database\Seeder;
use App\ReferCommission;

class ReferCommissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $commission_percentage = [[
            'commission_percentage' => 10,
        ]];

        ReferCommission::insert($commission_percentage);
    }
}
