<?php

use Illuminate\Database\Seeder;
use App\OtpLogin;

class OtpLoginsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $otp_logins = [[
            'phone_no'           => '9999988888',
            'otp_no'           => '123456',
            'is_active'          => 1,
        ]];

        OtpLogin::insert($otp_logins);
    }
}
