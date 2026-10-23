<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $table = 'tmp_student_profiles';

    protected $fillable = [
        'user_id',
        'parent_id',
        'grade_level',
        'education_board',
        'timezone',
        'learning_goals',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
