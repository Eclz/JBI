<?php

namespace App\Http\Controllers\Faculty\HOD;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Grade;
use App\Models\LeaveRequest;
use App\Models\Assignment;
use App\Models\ProgramChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationsController extends Controller
{
    private function getDepartment()
    {
        return Department::with(['facultyMembers.user', 'programs', 'courses'])
            ->where('head_of_department_id', auth()->id())
            ->firstOrFail();
    }

    public function index()
    {
        $department = $this->getDepartment();

        // 1. Leave Requests from Faculty in this department
        $facultyUserIds = $department->facultyMembers->pluck('user_id');
        $pendingLeaves = LeaveRequest::whereIn('user_id', $facultyUserIds)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        // 2. Program Change Requests coming into this department's programs
        $programIds = $department->programs->pluck('id');
        $pendingProgramChanges = ProgramChangeRequest::whereIn('requested_program_id', $programIds)
            ->where('status', 'pending')
            ->with(['student', 'currentProgram', 'requestedProgram'])
            ->get();

        // 3. Grade Moderation: find assignments/exams in this department's courses that have unpublished grades
        $courseIds = $department->courses->pluck('id');
        $pendingGradesCount = Grade::whereIn('course_id', $courseIds)
            ->where('is_published', false)
            ->count();

        $assignmentsToModerate = Assignment::whereIn('course_id', $courseIds)
            ->whereHas('grades', function ($q) {
                $q->where('is_published', false);
            })
            ->with(['course', 'grades' => function ($q) {
                $q->where('is_published', false);
            }])
            ->get();

        return view('faculty.hod.operations.index', compact(
            'department', 'pendingLeaves', 'pendingProgramChanges', 'pendingGradesCount', 'assignmentsToModerate'
        ));
    }

    public function approveLeave(Request $request, LeaveRequest $leave)
    {
        $department = $this->getDepartment();
        if (!$department->facultyMembers->contains('user_id', $leave->user_id)) {
            abort(403);
        }

        $leave->update([
            'status' => 'approved',
            'manager_id' => auth()->id(),
            'manager_comment' => $request->input('manager_comment', 'Approved by HOD.'),
        ]);

        return back()->with('success', 'Leave request approved.');
    }

    public function rejectLeave(Request $request, LeaveRequest $leave)
    {
        $department = $this->getDepartment();
        if (!$department->facultyMembers->contains('user_id', $leave->user_id)) {
            abort(403);
        }

        $leave->update([
            'status' => 'rejected',
            'manager_id' => auth()->id(),
            'manager_comment' => $request->input('manager_comment', 'Rejected by HOD.'),
        ]);

        return back()->with('success', 'Leave request rejected.');
    }

    public function endorseProgramChange(Request $request, ProgramChangeRequest $programChange)
    {
        $department = $this->getDepartment();
        if (!in_array($programChange->requested_program_id, $department->programs->pluck('id')->toArray())) {
            abort(403);
        }

        // We can just add a note that the HOD endorsed it. The Registrar will do the final approval.
        $existingNotes = $programChange->review_notes;
        $endorsement = "Endorsed by HOD ({$department->name}) on " . now()->format('Y-m-d H:i') . ".";
        
        $programChange->update([
            'review_notes' => $existingNotes ? $existingNotes . "\n" . $endorsement : $endorsement,
        ]);

        return back()->with('success', 'Program change request endorsed successfully. Sent to Registrar.');
    }

    public function moderateGrades(Request $request, Assignment $assignment)
    {
        $department = $this->getDepartment();
        if ($assignment->course->department_id !== $department->id) {
            abort(403);
        }

        // Publish all pending grades for this assignment
        Grade::where('assignment_id', $assignment->id)
            ->where('is_published', false)
            ->update([
                'is_published' => true,
            ]);

        return back()->with('success', 'Grades moderated and published successfully.');
    }
}
