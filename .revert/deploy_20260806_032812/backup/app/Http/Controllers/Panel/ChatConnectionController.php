<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ChatConnection;
use App\Models\PublicChatConversation;
use App\User;
use Illuminate\Http\Request;

class ChatConnectionController extends Controller
{
    /**
     * Show connections page: search, pending requests, accepted contacts.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Incoming pending requests (others sent to me)
        $pendingRequests = ChatConnection::where('receiver_id', $user->id)
            ->where('status', ChatConnection::$pending)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();

        // Accepted connections
        $connections = ChatConnection::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
        })->where('status', ChatConnection::$accepted)
            ->with(['sender', 'receiver'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($c) use ($user) {
                $c->other_party = ($c->sender_id == $user->id) ? $c->receiver : $c->sender;
                return $c;
            });

        // Outgoing pending requests (I sent, awaiting their response)
        $sentRequests = ChatConnection::where('sender_id', $user->id)
            ->where('status', ChatConnection::$pending)
            ->with('receiver')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'pageTitle'      => 'Connections',
            'pendingRequests' => $pendingRequests,
            'connections'    => $connections,
            'sentRequests'   => $sentRequests,
        ];

        return view('design_1.panel.connections.index', $data);
    }

    /**
     * Search users to send a request to.
     */
    public function searchUsers(Request $request)
    {
        $user  = auth()->user();
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::where('id', '!=', $user->id)
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('full_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'full_name', 'email', 'avatar')
            ->limit(10)
            ->get()
            ->map(function ($u) use ($user) {
                $connection = ChatConnection::between($user->id, $u->id);
                return [
                    'id'         => $u->id,
                    'full_name'  => $u->full_name,
                    'email'      => $u->email,
                    'avatar'     => $u->getAvatar(40),
                    'connection_status' => $connection ? $connection->status : null,
                    'connection_id'     => $connection ? $connection->id : null,
                    'is_sender'  => $connection ? ($connection->sender_id == $user->id) : null,
                ];
            });

        return response()->json(['users' => $users]);
    }

    /**
     * Send a connection request.
     */
    public function sendRequest(Request $request)
    {
        $user       = auth()->user();
        $receiverId = (int) $request->input('receiver_id');

        if ($receiverId == $user->id) {
            return response()->json(['error' => 'You cannot send a request to yourself.'], 422);
        }

        $receiver = User::find($receiverId);
        if (!$receiver) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Check if a connection already exists
        $existing = ChatConnection::between($user->id, $receiverId);
        if ($existing) {
            return response()->json(['error' => 'A connection request already exists.'], 422);
        }

        $connection = ChatConnection::create([
            'sender_id'   => $user->id,
            'receiver_id' => $receiverId,
            'status'      => ChatConnection::$pending,
            'created_at'  => time(),
            'updated_at'  => time(),
        ]);

        return response()->json(['success' => true, 'connection_id' => $connection->id]);
    }

    /**
     * Accept a pending connection request.
     */
    public function acceptRequest($id)
    {
        $user       = auth()->user();
        $connection = ChatConnection::where('id', $id)
            ->where('receiver_id', $user->id)
            ->where('status', ChatConnection::$pending)
            ->first();

        if (!$connection) {
            return response()->json(['error' => 'Request not found.'], 404);
        }

        $connection->update([
            'status'     => ChatConnection::$accepted,
            'updated_at' => time(),
        ]);

        // Auto-create a direct chat conversation between the two users
        PublicChatConversation::firstOrCreate([
            'user_id'      => $connection->sender_id,
            'instructor_id' => $connection->receiver_id,
            'source_type'  => 'direct',
            'source_id'    => 0,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Decline a pending connection request.
     */
    public function declineRequest($id)
    {
        $user       = auth()->user();
        $connection = ChatConnection::where('id', $id)
            ->where('receiver_id', $user->id)
            ->where('status', ChatConnection::$pending)
            ->first();

        if (!$connection) {
            return response()->json(['error' => 'Request not found.'], 404);
        }

        $connection->update([
            'status'     => ChatConnection::$declined,
            'updated_at' => time(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove/block a connection.
     */
    public function removeConnection($id)
    {
        $user       = auth()->user();
        $connection = ChatConnection::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
            })->first();

        if (!$connection) {
            return response()->json(['error' => 'Connection not found.'], 404);
        }

        $connection->delete();

        return response()->json(['success' => true]);
    }
}
