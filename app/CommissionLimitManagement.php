<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommissionLimitManagement extends Model
{
    public $table = "commission_limit_managements";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'refer_commission_percentage',
        'wallet_withdraw_limit',
        'refer_reedem_limit',
        'max_refer_commission',
        'pending_game_penalty_amt',
        'wrong_result_penalty_amt',
        'created_at',
        'updated_at',
    ];
}
