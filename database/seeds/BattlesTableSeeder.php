<?php

use Illuminate\Database\Seeder;
use App\Battle;

class BattlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $battles = [[
            'game_listing_id'           => 1,
            'price'          => 500,
            'entry fees'       => 300,
            'live player'       => 1,
        ]];

        Battle::insert($battles);
    }
}
