<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'tmp_subjects';

    protected $fillable = [
        'name',
        'category',
    ];

    public function teachers()
    {
        return $this->belongsToMany(TeacherProfile::class, 'tmp_teacher_subjects', 'subject_id', 'teacher_id')
                    ->withPivot('grade_range');
    }
}
