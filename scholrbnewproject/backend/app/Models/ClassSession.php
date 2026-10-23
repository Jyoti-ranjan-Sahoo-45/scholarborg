<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $table = 'tmp_classes';

    protected $fillable = [
        'booking_id',
        'teams_meeting_id',
        'teams_join_url',
        'agora_channel_name',
        'agora_token',
        'actual_start',
        'actual_end',
    ];

    protected $casts = [
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }
}
