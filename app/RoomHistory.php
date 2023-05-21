<?php

namespace App;
use App\Room;
use App\User;

use Illuminate\Database\Eloquent\Model;

class RoomHistory extends Model
{
    public $table = "room_historys";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'room_id',
        'player_id',
        'game_id',
        'game_name',
        'player_name',
        'room_code',
        'screenshot',
        'player_shared_status',
        'admin_provided_status',
        'penalty_status',
        'entry_fees',
        'price',
        'admin_commission',
        'cancel_note',
        'created_at',
        'updated_at',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class,'room_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'player_id');
    }
}
