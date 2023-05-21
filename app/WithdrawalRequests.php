<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequests extends Model
{
    public $table = "withdrawal_requests";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'description',
        'transferId',
        'referenceId',
        'beneficiary_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
