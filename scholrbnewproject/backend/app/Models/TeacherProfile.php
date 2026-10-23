<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $table = 'tmp_teachers';

    protected $fillable = [
        'user_id',
        'qualification',
        'experience_years',
        'bio',
        'teaching_methodology',
        'hourly_rate',
        'demo_rate',
        'intro_video_url',
        'verification_status',
        'rating_avg',
        'reviews_count',
        'languages_spoken',
        'teaching_modes',
    ];

    protected $casts = [
        'languages_spoken' => 'array',
        'teaching_modes' => 'array',
        'rating_avg' => 'float',
        'reviews_count' => 'integer',
        'hourly_rate' => 'float',
        'demo_rate' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifications()
    {
        return $this->hasMany(TeacherVerification::class, 'teacher_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'tmp_teacher_subjects', 'teacher_id', 'subject_id')
                    ->withPivot('grade_range');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class, 'teacher_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'teacher_id');
    }
}
