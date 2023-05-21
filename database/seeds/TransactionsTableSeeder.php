<?php

use Illuminate\Database\Seeder;
use App\Transaction;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transactions = [[
            'user_id'           => 1,
            'debit'          => 500,
            'credit'        => 0,
            'transaction_type' =>'paytm',
            'date' =>'2022-09-24',
            'time' =>'11:30:30',
            'mobile_no' =>'9900998789',
            'comment' =>'500 rupees debited',
        ],
        [
            'user_id'           => 1,
            'debit'          => 0,
            'credit'        => 200,
            'transaction_type' =>'phone pe',
            'date' =>'2022-09-26',
            'time' =>'11:35:30',
            'mobile_no' =>'8899789090',
            'comment' =>'200 rupees credited',
        ],
        [
            'user_id'           => 1,
            'debit'          => 0,
            'credit'        => 200,
            'transaction_type' =>'paytm',
            'date' =>'2022-09-25',
            'time' =>'12:30:30',
            'mobile_no' =>'9900998789',
            'comment' =>'200 rupees credited',
        ]
    ];

        Transaction::insert($transactions);
    }
}
