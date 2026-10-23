<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'tmp_packages';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'total_classes',
        'used_classes',
        'total_amount',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'total_classes' => 'integer',
        'used_classes' => 'integer',
        'total_amount' => 'float',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'package_id');
    }
}
