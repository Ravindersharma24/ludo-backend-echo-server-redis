<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Gamelisting;
use App\User;

class Battle extends Model
{
    public $table = "battles";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'game_listing_id',
        'user_id',
        'price',
        'entry_fees',
        'admin_commission',
        'battle_status',
        'created_at',
        'updated_at',
    ];

    public function gamelisting()
    {
        // return $this->belongsToMany(Kyc::class);
        return $this->belongsTo(Gamelisting::class,'game_listing_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
