<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    protected $table = 'call_logs';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function caller()
    {
        return $this->belongsTo('App\User', 'caller_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\User', 'receiver_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo('App\Models\ReserveMeeting', 'booking_id', 'id');
    }

    public function auditedBy()
    {
        return $this->belongsTo('App\User', 'audited_by', 'id');
    }
}
