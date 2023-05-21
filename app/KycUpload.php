<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Document;
use App\State;

class KycUpload extends Model
{
    public $table = "kyc_uploads";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        // 'document_type',
        'document_id',
        'user_id',
        'document_number',
        'first_name',
        'last_name',
        'dob',
        'state_id',
        'front_photo',
        'back_photo',
        'kyc_status',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class,'document_id');
    }
    public function state()
    {
        return $this->belongsTo(State::class,'state_id');
    }
}
