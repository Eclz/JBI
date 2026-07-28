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
