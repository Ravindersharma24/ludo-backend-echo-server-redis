<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TempTransaction extends Model
{
    public $table = "temp_transactions";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'user_id',
        'username',
        'balance',
        'deposit_cash',
        'winning_cash',
        'transaction_type',
        'battle_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        // return $this->belongsToMany(Kyc::class);
        return $this->belongsTo(User::class,'user_id');
    }
}
