<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return view('notifications.show', compact('notification'));
    }

    public function retryEmail($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        if ($notification->email_sent) {
            return back()->with('success', 'This email has already been sent.');
        }
        if (($notification->data['email_status'] ?? null) === 'queued') {
            return back()->with('warning', 'This email is already queued. Contact the administrator if it remains queued.');
        }
        $notification->updateQuietly(['data' => array_merge($notification->data ?? [], ['email_status' => 'queued'])]);
        \App\Jobs\SendNotificationEmail::dispatch($notification->id);
        return back()->with('success', 'Email queued for retry.');
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    public function deleteAll()
    {
        Auth::user()->notifications()->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'All notifications have been deleted successfully.');
    }
}
