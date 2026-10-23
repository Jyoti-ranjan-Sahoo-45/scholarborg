<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    protected $table = 'tmp_escrows';

    protected $fillable = [
        'booking_id',
        'teacher_id',
        'amount',
        'platform_commission',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
        'platform_commission' => 'float',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }
}
