<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenaltyHistory extends Model
{
    public $table = "penalty_histories";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'user_id',
        'username',
        'mobile_no',
        'battle_id',
        'penalty_type',
        'transaction_type',
        'penalty_amount',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        // return $this->belongsToMany(Kyc::class);
        return $this->belongsTo(User::class,'user_id');
    }
}
