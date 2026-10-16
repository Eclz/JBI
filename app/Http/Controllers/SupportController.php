<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminOrStaff = $user->isAdmin() || $user->isSuperAdmin() || $user->hasRole('admin');

        $myTickets = SupportTicket::where('user_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'my_page');

        $adminTickets = null;
        if ($isAdminOrStaff) {
            $adminTickets = SupportTicket::with(['user', 'assignedStaff'])
                ->when($request->status, fn($q, $s) => $q->where('status', $s))
                ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
                ->latest()
                ->paginate(15, ['*'], 'admin_page');
        }

        return view('support.index', compact('myTickets', 'adminTickets', 'isAdminOrStaff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'message' => 'required|string|max:3000',
        ]);

        $user = Auth::user();

        $ticket = SupportTicket::create([
            'ticket_number' => 'TIC-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
            'user_id' => $user->id,
            'subject' => $request->subject,
            'category' => $request->category ?? 'general',
            'priority' => $request->priority,
            'status' => 'open',
            'message' => $request->message,
        ]);

        \Log::info('Support Ticket Created', [
            'ticket_number' => $ticket->ticket_number,
            'user_id' => $user->id,
            'user_name' => $user->full_name,
            'subject' => $ticket->subject,
            'priority' => $ticket->priority,
        ]);

        // Create notification for the user
        Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Support Ticket Submitted',
            'message' => "Your ticket #{$ticket->ticket_number} ('{$ticket->subject}') has been received by JBI Support. Our team will review it shortly.",
            'action_url' => route('support.index'),
        ]);

        return redirect()->route('support.index')
            ->with('success', "Your support ticket #{$ticket->ticket_number} has been submitted successfully. We will get back to you soon.");
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSuperAdmin() && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized access to update support ticket.');
        }

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:3000',
        ]);

        $ticket->status = $validated['status'];
        $ticket->admin_notes = $validated['admin_notes'];
        if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
            $ticket->resolved_at = now();
        }
        $ticket->assigned_to = $user->id;
        $ticket->save();

        // Notify ticket owner
        Notification::create([
            'user_id' => $ticket->user_id,
            'type' => $ticket->status === 'resolved' ? 'success' : 'info',
            'title' => "Support Ticket #{$ticket->ticket_number} Status: " . ucfirst(str_replace('_', ' ', $ticket->status)),
            'message' => "Your support ticket #{$ticket->ticket_number} has been updated to " . ucfirst(str_replace('_', ' ', $ticket->status)) . ($ticket->admin_notes ? ". Staff note: " . Str::limit($ticket->admin_notes, 100) : "."),
            'action_url' => route('support.index'),
        ]);

        return redirect()->route('support.index')
            ->with('success', "Ticket #{$ticket->ticket_number} updated successfully.");
    }
}
