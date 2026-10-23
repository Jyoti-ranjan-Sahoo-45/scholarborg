<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentVault extends Model
{
    use HasFactory;

    protected $table = 'tmp_assignments_vault';

    protected $fillable = [
        'class_id',
        'teacher_id',
        'student_id',
        'title',
        'description',
        'file_url',
        'due_date',
        'submission_url',
        'submission_status',
        'grade_score',
        'teacher_feedback',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_id');
    }
}
