<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'tmp_bookings';

    protected $fillable = [
        'booking_uuid',
        'student_id',
        'teacher_id',
        'package_id',
        'booking_type',
        'scheduled_start',
        'scheduled_end',
        'lock_expires_at',
        'status',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'lock_expires_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function classSession()
    {
        return $this->hasOne(ClassSession::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function escrow()
    {
        return $this->hasOne(Escrow::class, 'booking_id');
    }
}
