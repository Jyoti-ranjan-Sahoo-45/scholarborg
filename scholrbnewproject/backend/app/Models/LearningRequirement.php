<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningRequirement extends Model
{
    use HasFactory;

    protected $table = 'tmp_learning_requirements';

    protected $fillable = [
        'user_id',
        'title',
        'subject_id',
        'subject_name',
        'grade_level',
        'board_syllabus',
        'mode',
        'budget_min',
        'budget_max',
        'preferred_timings',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proposals()
    {
        return $this->hasMany(TutorProposal::class, 'requirement_id');
    }
}
