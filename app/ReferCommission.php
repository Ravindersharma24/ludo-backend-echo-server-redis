<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferCommission extends Model
{
    public $table = "refer_commissions";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'commission_percentage',
        'created_at',
        'updated_at',
    ];
}
