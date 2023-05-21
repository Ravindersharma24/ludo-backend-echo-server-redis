<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BattleManagement extends Model
{
    public $table = "battle_managements";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'minimum_amount',
        'maximum_battle',
        'created_at',
        'updated_at',
    ];
}
