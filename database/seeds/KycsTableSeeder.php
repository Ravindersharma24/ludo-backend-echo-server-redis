<?php

use Illuminate\Database\Seeder;
use App\Kyc;

class KycsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kycs = [[
            'path'           => '1663569236.png',
            'document_id'          => 1,
            'user_id'       => 1,
        ]];

        Kyc::insert($kycs);
    }
}
