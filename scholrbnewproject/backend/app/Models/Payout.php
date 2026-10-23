<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $table = 'tmp_payouts';

    protected $fillable = [
        'teacher_id',
        'amount',
        'bank_account_details',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }
}
