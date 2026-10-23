<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * List active conversation threads with last message.
     */
    public function getConversations(Request $request)
    {
        $userId = $request->user()->id;

        // Get distinct user IDs that have exchanged messages with this user
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $conversations = [];
        $seen = [];

        foreach ($messages as $msg) {
            $otherId = ($msg->sender_id === $userId) ? $msg->receiver_id : $msg->sender_id;
            if (!in_array($otherId, $seen)) {
                $seen[] = $otherId;
                $otherUser = User::find($otherId);
                if ($otherUser) {
                    $conversations[] = [
                        'user' => [
                            'id' => $otherUser->id,
                            'name' => $otherUser->name,
                            'email' => $otherUser->email,
                            'type' => $otherUser->type,
                        ],
                        'last_message' => $msg->message,
                        'last_message_time' => $msg->created_at->diffForHumans(),
                        'unread_count' => Message::where('sender_id', $otherId)->where('receiver_id', $userId)->where('is_read', 0)->count(),
                    ];
                }
            }
        }

        return response()->json([
            'conversations' => $conversations
        ]);
    }

    /**
     * Get full message thread between logged-in user and recipient.
     */
    public function getThread(Request $request, $otherUserId)
    {
        $userId = $request->user()->id;

        $messages = Message::where(function ($q) use ($userId, $otherUserId) {
            $q->where('sender_id', $userId)->where('receiver_id', $otherUserId);
        })->orWhere(function ($q) use ($userId, $otherUserId) {
            $q->where('sender_id', $otherUserId)->where('receiver_id', $userId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark incoming messages as read
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Send direct message.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string',
            'requirement_id' => 'nullable|integer',
            'attachment_url' => 'nullable|string',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'requirement_id' => $request->requirement_id,
            'message' => $request->message,
            'attachment_url' => $request->attachment_url,
            'is_read' => 0,
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }
}
