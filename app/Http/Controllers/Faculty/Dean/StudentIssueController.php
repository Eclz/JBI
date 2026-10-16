<?php

namespace App\Http\Controllers\Faculty\Dean;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Faculty\Dean\Concerns\LoadsDeanScopedData;
use App\Models\StudentIssue;
use Illuminate\Http\Request;

class StudentIssueController extends Controller
{
    use LoadsDeanScopedData;

    public function index()
    {
        $issues = StudentIssue::with('student')->where('reported_by', auth()->id())->latest()->paginate(15);
        return view('faculty.dean.student-issues.index', compact('issues'));
    }

    public function create()
    {
        $students = $this->deanStudentsQuery()
            ->orderBy('first_name')
            ->orderBy('name')
            ->get();

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
        $isEscalate = $request->boolean('escalate') || $request->input('submit_action') === 'escalate';
        $validated['status'] = $isEscalate ? 'Escalated' : 'Open';

        StudentIssue::create($validated);

        $message = $isEscalate ? 'Student issue reported and escalated to administration.' : 'Student issue reported successfully.';
        return redirect()->route('faculty.dean.student-issues.index')->with('success', $message);
    }

    public function show(StudentIssue $studentIssue)
    {
        if ($studentIssue->reported_by !== auth()->id()) abort(403);
        return view('faculty.dean.student-issues.show', compact('studentIssue'));
    }

    public function edit(StudentIssue $studentIssue)
    {
        if ($studentIssue->reported_by !== auth()->id()) abort(403);
        
        $students = $this->deanStudentsQuery()
            ->orderBy('first_name')
            ->orderBy('name')
            ->get();

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
