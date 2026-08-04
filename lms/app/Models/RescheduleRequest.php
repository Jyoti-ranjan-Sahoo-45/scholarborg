<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RescheduleRequest extends Model
{
    protected $table = 'reschedule_requests';
    public $timestamps = false;
    protected $guarded = ['id'];

    public static $pending = 'pending';
    public static $approved = 'approved';
    public static $rejected = 'rejected';

    public function reserveMeeting()
    {
        return $this->belongsTo(ReserveMeeting::class, 'reserve_meeting_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(\App\User::class, 'requested_by');
    }
}
