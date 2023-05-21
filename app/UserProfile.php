<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserProfile extends Model
{
    public $table = "user_profiles";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'kyc_upload',
        'kyc_verified',
        'user_id',
        'mobile',
        'cash_won',
        'battle_played',
        'kyc_link',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
