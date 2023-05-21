<?php

use Illuminate\Database\Seeder;
use App\Gamelisting;


class GameListingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gamelistings = [[
            'name'           => 'Ludo Classic',
            'image'          => 'ludo.png',
            'description'       => 'ludo game',
            'status' => true,
            'room_code_url' => 'http://193.187.129.22:3002/api/popular/roomcode',
        ],
        [
            'name'           => 'Snake Classic',
            'image'          => 'snake.png',
            'description'       => 'snake game',
            'status' => true,
            'room_code_url' => 'http://193.187.129.22:3002/api/popular/roomcode',
        ]
    ];

        Gamelisting::insert($gamelistings);
    }
}
