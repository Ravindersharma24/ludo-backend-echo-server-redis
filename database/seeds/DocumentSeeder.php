<?php

use Illuminate\Database\Seeder;
use App\Document;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documents = [
            [
                'document_type'  => 'AADHAAR CARD',
            ],
            [
                'document_type'  => 'DRIVING LICENSE',
            ],
            [
                'document_type'  => 'VOTER ID CARD',
            ]
        ];

        Document::insert($documents);
    }
}
