<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $users = User::where('is_active', true)->get();
        $recentNotifications = Notification::orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.notifications.index', compact('roles', 'users', 'recentNotifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_type' => 'required|in:all,role,user',
            'target_role' => 'required_if:target_type,role',
            'target_user' => 'required_if:target_type,user',
            'priority' => 'required|in:low,normal,high,urgent',
            'action_url' => 'nullable|url',
        ]);

        $usersToNotify = collect();

        if ($request->target_type === 'all') {
            $usersToNotify = User::where('is_active', true)->get();
        } elseif ($request->target_type === 'role') {
            $usersToNotify = User::where('role_id', $request->target_role)->where('is_active', true)->get();
        } elseif ($request->target_type === 'user') {
            $user = User::find($request->target_user);
            if ($user) {
                $usersToNotify->push($user);
            }
        }

        $notifications = [];
        $now = now();
        
        foreach ($usersToNotify as $user) {
            $notifications[] = [
                'user_id' => $user->id,
                'type' => 'system',
                'title' => $request->title,
                'message' => $request->message,
                'action_url' => $request->action_url,
                'priority' => $request->priority,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($notifications)) {
            // Chunking for performance if there are many users
            foreach (array_chunk($notifications, 500) as $chunk) {
                Notification::insert($chunk);
            }
        }

        return back()->with('success', count($notifications) . ' notification(s) sent successfully.');
    }
}
