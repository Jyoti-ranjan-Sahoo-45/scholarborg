<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'leads';
    
    public $timestamps = false;
    
    protected $guarded = ['id'];

    protected $casts = [
        'otp_expires_at' => 'integer',
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];
}
