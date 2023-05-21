<?php

use Illuminate\Database\Seeder;
use App\Game;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $games = [[
            'time_play'           => 30,
            'user_id'          => 1,
            'result'       => 1,
        ]];

        Game::insert($games);
    }
}
