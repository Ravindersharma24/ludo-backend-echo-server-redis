<?php

use Illuminate\Database\Seeder;
use App\State;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
            [
                'state'  => 'Rajasthan',
            ],
            [
                'state'  => 'Delhi',
            ],
            [
                'state'  => 'Madhya Pradesh',
            ],
            [
                'state'  => 'Gujarat',
            ],
            [
                'state'  => 'Haryana',
            ],
        ];

        State::insert($states);
    }
}
