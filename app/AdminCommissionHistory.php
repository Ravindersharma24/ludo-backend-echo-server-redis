<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminCommissionHistory extends Model
{
    public $table = "admin_commission_histories";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'battle_id',
        'game_name',
        'room_id',
        'room_code',
        'entry_fees',
        'price',
        'admin_commission',
        'transaction_type',
        'created_at',
        'updated_at',
    ];
}
