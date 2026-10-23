<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherVerification extends Model
{
    protected $table = 'tmp_teacher_verifications';

    protected $fillable = [
        'teacher_id',
        'document_type',
        'document_path',
        'status',
        'admin_comments',
        'verified_by',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
