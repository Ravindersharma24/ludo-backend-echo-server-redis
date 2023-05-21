<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Gamelisting;

class Room extends Model
{
    public $table = "rooms";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'id',
        'battle_id',
        'game_id',
        'code',
        'status',
        'game_name',
        'created_at',
        'updated_at',
    ];

    public function gamelisting()
    {
        return $this->belongsTo(Gamelisting::class,'game_id');
    }
}
