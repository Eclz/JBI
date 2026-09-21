<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasPermission('human_resources', 'view')) {
            $leaves = LeaveRequest::with('user')->latest()->get();
        } else {
            $leaves = LeaveRequest::where('user_id', $user->id)->latest()->get();
        }
        return view('human-resources.leaves.index', compact('leaves'));
    }

    public function create()
    {
        $faculty = [];
        if (Auth::user()->isFaculty()) {
            $faculty = User::where('role', 'faculty')
                ->where('id', '!=', Auth::id())
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
        return view('human-resources.leaves.create', compact('faculty'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'substitute_id' => 'nullable|exists:users,id',
        ]);
        
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        LeaveRequest::create($data);

        return redirect()->route('human-resources.leaves.index')->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leave)
    {
        // Check authorization
        if (Auth::id() !== $leave->user_id && !Auth::user()->hasPermission('human_resources', 'view')) {
            abort(403);
        }
        
        return view('human-resources.leaves.show', compact('leave'));
    }

    public function edit(LeaveRequest $leave)
    {
        if (Auth::id() !== $leave->user_id || $leave->status !== 'pending') {
            abort(403);
        }

        $faculty = [];
        if (Auth::user()->isFaculty()) {
            $faculty = User::where('role', 'faculty')
                ->where('id', '!=', Auth::id())
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('human-resources.leaves.edit', compact('leave', 'faculty'));
    }

    public function update(Request $request, LeaveRequest $leave)
    {
        if (Auth::id() !== $leave->user_id || $leave->status !== 'pending') {
            abort(403);
        }

        $data = $request->validate([
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'substitute_id' => 'nullable|exists:users,id',
        ]);

        $leave->update($data);

        return redirect()->route('human-resources.leaves.index')->with('success', 'Leave request updated successfully.');
    }

    public function destroy(LeaveRequest $leave)
    {
        if (Auth::id() !== $leave->user_id || $leave->status !== 'pending') {
            abort(403);
        }

        $leave->delete();

        return redirect()->route('human-resources.leaves.index')->with('success', 'Leave request cancelled.');
    }

    public function updateStatus(Request $request, LeaveRequest $leave)
    {
        if (!Auth::user()->hasPermission('human_resources', 'view')) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'manager_comment' => 'nullable|string',
        ]);

        $leave->update([
            'status' => $request->status,
            'manager_comment' => $request->manager_comment,
            'manager_id' => Auth::id(),
        ]);

        return redirect()->route('human-resources.leaves.show', $leave)->with('success', 'Leave request status updated.');
    }
}
