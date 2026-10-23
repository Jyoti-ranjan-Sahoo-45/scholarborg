<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePackage extends Model
{
    use HasFactory;

    protected $table = 'tmp_course_packages';

    protected $fillable = [
        'teacher_id',
        'name',
        'total_classes',
        'discount_percent',
        'total_price',
        'is_active',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }
}
