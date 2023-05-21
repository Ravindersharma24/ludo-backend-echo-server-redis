<?php

namespace App;

use App\MannualTransaction;
use App\User;
use App\WithdrawalRequests;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    public $table = "transaction_histories";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'user_id',
        'paytm_withdraw_id',
        'mannual_withdraw_id',
        'username',
        'transaction_amount',
        'dr_cr',
        'order_id',
        'transaction_type',
        'status',
        'closing_balance',
        'game_image',
        'opposition_player',
        'battle_id',
        'game_name',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function mannual_withdrawl()
    {
        return $this->belongsTo(MannualTransaction::class, 'mannual_withdraw_id');
    }
    public function paytm_withdrawl()
    {
        return $this->belongsTo(WithdrawalRequests::class, 'paytm_withdraw_id');
    }
}
