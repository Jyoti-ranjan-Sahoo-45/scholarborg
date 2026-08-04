<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ParentStudent extends Model
{
    protected $table = 'parent_students';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }
}
