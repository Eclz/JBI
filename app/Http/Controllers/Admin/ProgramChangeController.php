<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramChangeRequest;
use App\Models\Program;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramChangeController extends Controller
{
    public function index(Request $request)
    {
        $query = ProgramChangeRequest::with([
            'student',
            'currentProgram.department',
            'requestedProgram.department',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.program-changes.index', compact('requests'));
    }

    public function create()
    {
        $programs = Program::with('department')->where('is_active', true)->get();
        $students = \App\Models\User::where('role', 'student')->has('studentProfile')->with('studentProfile.program')->get();
        return view('admin.program-changes.create', compact('programs', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'requested_program_id' => 'required|exists:programs,id',
            'reason' => 'required|string',
            'auto_approve' => 'nullable|boolean',
        ]);

        $student = \App\Models\User::with('studentProfile')->findOrFail($request->user_id);
        if (!$student->studentProfile) {
            return back()->withErrors(['error' => 'Student profile not found.']);
        }
        if ($student->studentProfile->program_id == $request->requested_program_id) {
            return back()->withErrors(['error' => 'Student is already in this program.']);
        }

        $existing = ProgramChangeRequest::where('user_id', $student->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existing) {
            return back()->withErrors(['error' => 'This student already has a pending program change request.']);
        }

        $change = ProgramChangeRequest::create([
            'user_id' => $student->id,
            'current_program_id' => $student->studentProfile->program_id,
            'requested_program_id' => $request->requested_program_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        if ($request->boolean('auto_approve')) {
            $request->merge(['review_notes' => 'Auto-approved by admin upon creation.']);
            return $this->approve($request, $change);
        }

        return redirect()->route('admin.program-changes.index')->with('success', 'Program change request created successfully.');
    }

    public function approve(Request $request, ProgramChangeRequest $programChange)
    {
        $request->validate([
            'review_notes' => 'nullable|string|max:2000',
        ]);

        if ($programChange->status !== 'pending') {
            return back()->withErrors(['error' => 'This request has already been processed.']);
        }

        DB::beginTransaction();

        try {
            $requestedProgram = Program::with('department')->findOrFail($programChange->requested_program_id);
            $profile = $programChange->student->studentProfile;

            if ($profile) {
                $profile->update([
                    'program_id' => $requestedProgram->id,
                    'program' => $requestedProgram->name,
                    'department_id' => $requestedProgram->department_id,
                ]);
            }

            $programChange->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $request->review_notes,
            ]);

            Notification::create([
                'user_id' => $programChange->user_id,
                'type' => 'program_change',
                'title' => 'Program Change Approved',
                'message' => 'Your program change request has been approved.',
                'priority' => 'high',
            ]);

            DB::commit();

            return back()->with('success', 'Program change request approved.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Program change approval failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to approve program change request.']);
        }
    }

    public function reject(Request $request, ProgramChangeRequest $programChange)
    {
        $request->validate([
            'review_notes' => 'nullable|string|max:2000',
        ]);

        if ($programChange->status !== 'pending') {
            return back()->withErrors(['error' => 'This request has already been processed.']);
        }

        $programChange->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        Notification::create([
            'user_id' => $programChange->user_id,
            'type' => 'program_change',
            'title' => 'Program Change Rejected',
            'message' => 'Your program change request has been rejected.',
            'priority' => 'normal',
        ]);

        return back()->with('success', 'Program change request rejected.');
    }
}
