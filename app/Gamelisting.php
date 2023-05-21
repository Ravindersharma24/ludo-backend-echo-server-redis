<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Battle;

class Gamelisting extends Model
{
    public $table = "gamelistings";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'name',
        'image',
        'description',
        'status',
        'room_code_url',
        'created_at',
        'updated_at',
    ];

    public function battles()
    {
      return $this->hasMany(Battle::class, 'game_listing_id');
    }
}
