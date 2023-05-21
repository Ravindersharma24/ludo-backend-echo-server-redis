<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MannualTransaction extends Model
{
    public $table = "mannual_transactions";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'phone_no',
        'amount',
        'status',
        'transfer_type',
        'upi_id',
        'account_number',
        'ifsc_code',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
