<?php

namespace App\Http\Controllers\Faculty\Dean;

use App\Http\Controllers\Controller;
use App\Models\StudentIssue;
use Illuminate\Http\Request;

class StudentIssueController extends Controller
{
    public function index()
    {
        $issues = StudentIssue::with('student')->where('reported_by', auth()->id())->latest()->paginate(15);
        return view('faculty.dean.student-issues.index', compact('issues'));
    }

    public function create()
    {
        $deanId = auth()->id();
        $students = \App\Models\User::where(function($query) {
                $query->where('role', 'student')
                      ->orWhereHas('roleCatalog', function ($q) {
                          $q->where('guard_role', 'student');
                      });
            })
            ->whereHas('studentProfile.department.faculty', function($q) use ($deanId) {
                $q->where('dean_id', $deanId);
            })
            ->orderBy('first_name')->get();

        return view('faculty.dean.student-issues.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'issue_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $validated['reported_by'] = auth()->id();
        $validated['status'] = 'Open';

        StudentIssue::create($validated);

        return redirect()->route('faculty.dean.student-issues.index')->with('success', 'Student issue reported successfully.');
    }

    public function show(StudentIssue $studentIssue)
    {
        if ($studentIssue->reported_by !== auth()->id()) abort(403);
        return view('faculty.dean.student-issues.show', compact('studentIssue'));
    }

    public function edit(StudentIssue $studentIssue)
    {
        if ($studentIssue->reported_by !== auth()->id()) abort(403);
        
        $deanId = auth()->id();
        $students = \App\Models\User::where(function($query) {
                $query->where('role', 'student')
                      ->orWhereHas('roleCatalog', function ($q) {
                          $q->where('guard_role', 'student');
                      });
            })
            ->whereHas('studentProfile.department.faculty', function($q) use ($deanId) {
                $q->where('dean_id', $deanId);
            })
            ->orderBy('first_name')->get();

        return view('faculty.dean.student-issues.edit', compact('studentIssue', 'students'));
    }

    public function update(Request $request, StudentIssue $studentIssue)
    {
        if ($studentIssue->reported_by !== auth()->id()) abort(403);

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'issue_type' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:Open,In Progress,Resolved,Escalated',
            'action_taken' => 'nullable|string',
        ]);

        $studentIssue->update($validated);

        return redirect()->route('faculty.dean.student-issues.index')->with('success', 'Student issue updated.');
    }

    public function destroy(StudentIssue $studentIssue)
    {
        if ($studentIssue->reported_by !== auth()->id()) abort(403);
        
        $studentIssue->delete();

        return redirect()->route('faculty.dean.student-issues.index')->with('success', 'Student issue deleted.');
    }
}
