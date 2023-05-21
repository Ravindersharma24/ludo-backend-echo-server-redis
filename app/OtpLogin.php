<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtpLogin extends Model
{
    public $table = "otp_logins";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'phone_no',
        'otp_no',
        'is_active',
        'created_at',
        'updated_at',
    ];
}
