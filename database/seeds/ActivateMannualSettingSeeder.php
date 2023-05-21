<?php

use App\ActivateMannaulSetting;
use Illuminate\Database\Seeder;

class ActivateMannualSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activate_mannual_setting = [
            [
                'setting_type' => 'Paytm Withdrawl',
                'status' => true,
            ],
            [
                'setting_type' => 'Manual Room Code',
                'status' => true,
            ],
    ];

        ActivateMannaulSetting::insert($activate_mannual_setting);
    }
}
