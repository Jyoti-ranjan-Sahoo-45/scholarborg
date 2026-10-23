<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'tmp_attendances';

    protected $fillable = [
        'class_id',
        'user_id',
        'role',
        'status',
        'join_time',
    ];

    protected $casts = [
        'join_time' => 'datetime',
    ];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
