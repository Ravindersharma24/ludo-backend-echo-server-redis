<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositHistory extends Model
{
    public $table = "deposit_historys";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'order_id',
        'amount',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
