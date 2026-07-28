<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramChangeController extends Controller
{
    public function index()
    {
        $requests = ProgramChangeRequest::where('user_id', Auth::id())
            ->with(['currentProgram', 'requestedProgram'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.program-changes.index', compact('requests'));
    }

    public function create()
    {
        $student = Auth::user();
        $profile = $student->studentProfile;

        $programs = Program::where('is_active', true)
            ->with(['department', 'level'])
            ->orderBy('name')
            ->get();

        return view('student.program-changes.create', compact('programs', 'profile'));
    }

    public function store(Request $request)
    {
        $student = Auth::user();
        $profile = $student->studentProfile;

        if (!$profile) {
            return back()->withErrors(['error' => 'Student profile not found.']);
        }

        $request->validate([
            'requested_program_id' => 'required|exists:programs,id',
            'reason' => 'required|string|max:2000',
        ]);

        if ($profile->program_id && $profile->program_id == $request->requested_program_id) {
            return back()->withErrors(['error' => 'You are already in this program.']);
        }

        ProgramChangeRequest::create([
            'user_id' => $student->id,
            'current_program_id' => $profile->program_id,
            'requested_program_id' => $request->requested_program_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('student.program-changes.index')
            ->with('success', 'Program change request submitted successfully.');
    }
}
