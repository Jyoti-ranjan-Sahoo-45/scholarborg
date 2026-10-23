<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'tmp_payments';

    protected $fillable = [
        'transaction_reference',
        'user_id',
        'booking_id',
        'package_id',
        'amount',
        'gateway_name',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
