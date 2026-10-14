<?php

namespace App\Http\Controllers\Faculty\Dean;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Faculty\Dean\Concerns\LoadsDeanScopedData;
use App\Models\FacultyEvaluation;
use Illuminate\Http\Request;

class FacultyEvaluationController extends Controller
{
    use LoadsDeanScopedData;

    public function index()
    {
        $evaluations = FacultyEvaluation::with('faculty')
            ->where('evaluator_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('faculty.dean.evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $facultyMembers = $this->deanFacultyMembersQuery()
            ->orderBy('first_name')
            ->orderBy('name')
            ->get();

        return view('faculty.dean.evaluations.create', compact('facultyMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:users,id',
            'academic_year' => 'required|string|max:50',
            'performance_score' => 'required|integer|min:0|max:100',
            'comments' => 'required|string',
        ]);

        $validated['evaluator_id'] = auth()->id();
        $validated['status'] = 'Draft';

        FacultyEvaluation::create($validated);

        return redirect()->route('faculty.dean.evaluations.index')->with('success', 'Faculty evaluation drafted successfully.');
    }

    public function show(FacultyEvaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        return view('faculty.dean.evaluations.show', compact('evaluation'));
    }

    public function edit(FacultyEvaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        if ($evaluation->status !== 'Draft') {
            return redirect()->route('faculty.dean.evaluations.index')->with('error', 'Only draft evaluations can be edited.');
        }

        $facultyMembers = $this->deanFacultyMembersQuery()
            ->orderBy('first_name')
            ->orderBy('name')
            ->get();
        
        return view('faculty.dean.evaluations.edit', compact('evaluation', 'facultyMembers'));
    }

    public function update(Request $request, FacultyEvaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        if ($evaluation->status !== 'Draft') {
            return redirect()->route('faculty.dean.evaluations.index')->with('error', 'Only draft evaluations can be edited.');
        }

        $validated = $request->validate([
            'faculty_id' => 'required|exists:users,id',
            'academic_year' => 'required|string|max:50',
            'performance_score' => 'required|integer|min:0|max:100',
            'comments' => 'required|string',
        ]);

        $evaluation->update($validated);

        return redirect()->route('faculty.dean.evaluations.index')->with('success', 'Faculty evaluation updated successfully.');
    }

    public function destroy(FacultyEvaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        if ($evaluation->status !== 'Draft') {
            return redirect()->route('faculty.dean.evaluations.index')->with('error', 'Only draft evaluations can be deleted.');
        }

        $evaluation->delete();

        return redirect()->route('faculty.dean.evaluations.index')->with('success', 'Faculty evaluation deleted.');
    }

    public function submit(FacultyEvaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        if ($evaluation->status !== 'Draft') {
            return back()->with('error', 'Only drafts can be submitted.');
        }

        $evaluation->update(['status' => 'Pending Approval']);

        return back()->with('success', 'Faculty evaluation submitted for approval.');
    }

    private function authorizeAccess(FacultyEvaluation $evaluation)
    {
        if ($evaluation->evaluator_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
