<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'all');

        $query = Message::with(['sender', 'receiver'])
            ->where(function($q) use ($user) {
                $q->where('receiver_id', $user->id)
                  ->orWhereNull('receiver_id'); // System broadcast alerts
            });

        if ($type === 'personal') {
            $query->where('type', 'message');
        } elseif ($type === 'alerts') {
            $query->whereIn('type', ['assignment_alert', 'quiz_alert', 'exam_alert', 'system']);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = Message::where(function($q) use ($user) {
            $q->where('receiver_id', $user->id)->orWhereNull('receiver_id');
        })->where('is_read', false)->count();

        // Get recipients (students and faculty)
        $recipients = User::where('id', '!=', $user->id)
            ->whereIn('role', ['student', 'faculty', 'admin'])
            ->select('id', 'first_name', 'last_name', 'email', 'role')
            ->get();

        return view('messages.index', compact('messages', 'unreadCount', 'recipients', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'type' => 'message',
            'is_read' => false,
        ]);

        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        $user = Auth::user();
        if ($message->receiver_id && $message->receiver_id !== $user->id && $message->sender_id !== $user->id) {
            abort(403);
        }

        if ($message->receiver_id === $user->id && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('messages.show', compact('message'));
    }
}
