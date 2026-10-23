<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $table = 'tmp_ledger_entries';

    protected $fillable = [
        'ledger_uuid',
        'user_id',
        'amount',
        'type',
        'description',
        'balance_before',
        'balance_after',
        'source_reference',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_before' => 'float',
        'balance_after' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
