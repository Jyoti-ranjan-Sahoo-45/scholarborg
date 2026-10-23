<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemRule extends Model
{
    protected $table = 'tmp_rules';

    protected $fillable = [
        'key',
        'value',
        'description',
        'version',
        'updated_by',
    ];

    protected $casts = [
        'value' => 'array',
        'version' => 'integer',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
