<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\KycUpload;

class Document extends Model
{
    public $table = "documents";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'document_type',
        'created_at',
        'updated_at',
    ];

    // public function kyc_upload()
    // {
    //     return $this->belongsTo(KycUpload::class,'document_id');
    // }
    // public function kyc_upload()
    // {
    //     return $this->hasMany(KycUpload::class);
    // }
}
