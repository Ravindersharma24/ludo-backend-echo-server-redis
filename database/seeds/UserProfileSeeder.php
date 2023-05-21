<?php

use Illuminate\Database\Seeder;
use App\UserProfile;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_profiles = [[
            'kyc_upload'           => 1,
            'kyc_verified'           => 1,
            'user_id'          => 1,
            'mobile'       => '9988650000',
            'cash_won' =>   100.00,
            'battle_played' => 5,
            'kyc_link' => "kyc_doc_path",
        ]];

        UserProfile::insert($user_profiles);
    }
}
