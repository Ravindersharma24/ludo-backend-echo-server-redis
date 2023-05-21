<?php

namespace App;

use App\Game;
use App\Kyc;
use App\KycUpload;
use App\Transaction;
use App\UserProfile;
use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // use UserReferral;

    use HasApiTokens, SoftDeletes, Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
    ];

    protected $fillable = [
        'name',
        'user_image',
        'phone_no',
        'email',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'active',
        'balance',
        'deposit_cash',
        'refer_cash',
        'affiliate_id',
        'created_battles',
        'otp',
        'upi_id',
        // 'affiliate_id'
    ];

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function kycs()
    {
        // return $this->belongsToMany(Kyc::class);
        return $this->hasMany(Kyc::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'user_id');
    }

    public function user_profiles()
    {
        return $this->hasMany(UserProfile::class, 'user_id');
    }

    public function toggle()
    {

        return $this->update(['active' => !$this->active]);
    }

    public function kyc_upload()
    {
        return $this->hasMany(KycUpload::class);
    }
}
