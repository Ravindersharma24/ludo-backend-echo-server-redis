<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminCommission extends Model
{
    public $table = "admin_commissions";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'commission_type',
        'from_amount',
        'to_amount',
        'commission_value',
        'condition',
        'created_at',
        'updated_at',
    ];
}
