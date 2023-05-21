<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    public $table = "referrals";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'user_id',
        'username',
        'referral_amount',
        'dr_cr',
        'transaction_type',
        'referral_closing_balance',
        'order_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        // return $this->belongsToMany(Kyc::class);
        return $this->belongsTo(User::class,'user_id');
    }
}
