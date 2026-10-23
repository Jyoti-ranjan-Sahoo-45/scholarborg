<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorProposal extends Model
{
    use HasFactory;

    protected $table = 'tmp_tutor_proposals';

    protected $fillable = [
        'requirement_id',
        'teacher_id',
        'proposed_hourly_rate',
        'pitch_message',
        'available_slot_id',
        'status',
    ];

    public function requirement()
    {
        return $this->belongsTo(LearningRequirement::class, 'requirement_id');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function slot()
    {
        return $this->belongsTo(TeacherAvailability::class, 'available_slot_id');
    }
}
