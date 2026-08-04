<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ChatConnection extends Model
{
    protected $table = 'chat_connections';
    public $timestamps = false;
    protected $guarded = ['id'];

    public static $pending  = 'pending';
    public static $accepted = 'accepted';
    public static $declined = 'declined';
    public static $blocked  = 'blocked';

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    /**
     * Check if two users have an accepted connection (in either direction).
     */
    public static function isConnected(int $userA, int $userB): bool
    {
        return static::where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA)->where('receiver_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB)->where('receiver_id', $userA);
        })->where('status', static::$accepted)->exists();
    }

    /**
     * Get connection record between two users (any direction).
     */
    public static function between(int $userA, int $userB)
    {
        return static::where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA)->where('receiver_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB)->where('receiver_id', $userA);
        })->first();
    }
}
