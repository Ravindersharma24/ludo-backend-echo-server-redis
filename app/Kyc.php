<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{

    public $table = "kycs";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'path',
        'document_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    // public function users()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
