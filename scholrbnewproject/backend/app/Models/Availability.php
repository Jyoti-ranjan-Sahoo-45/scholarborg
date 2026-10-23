<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $table = 'tmp_availabilities';

    protected $fillable = [
        'teacher_id',
        'day_of_week',
        'specific_date',
        'start_time',
        'end_time',
        'is_booked',
    ];

    protected $casts = [
        'specific_date' => 'date',
        'is_booked' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }
}
