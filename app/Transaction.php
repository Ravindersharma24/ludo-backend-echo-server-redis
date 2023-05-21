<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Transaction extends Model
{
    public $table = "transactions";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        // 'user_id',
        // 'debit',
        // 'created_at',
        // 'updated_at',
    ];

    public function user()
    {
        // return $this->belongsToMany(Kyc::class);
        return $this->belongsTo(User::class,'user_id');
    }
}
