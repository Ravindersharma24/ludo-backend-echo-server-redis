<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public $table = "states";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'state',
        'created_at',
        'updated_at',
    ];
}
