<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivateMannaulSetting extends Model
{
    public $table = "activate_mannual_settings";

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'setting_type',
        'status',
        'created_at',
        'updated_at',
    ];

    public function toggle()
    {
        return $this->update(['status' => !$this->status]);
    }
}
