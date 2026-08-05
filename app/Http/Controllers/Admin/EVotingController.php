<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VotingSession;
use App\Models\VotingPosition;
use App\Models\VotingCandidate;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class EVotingController extends Controller
{
    public function index()
    {
        $sessions = VotingSession::with(['academicYear', 'positions.candidates'])->orderBy('created_at', 'desc')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();

        return view('admin.evoting.index', compact('sessions', 'academicYears'));
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_semester' => 'required|integer|in:1,2',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        VotingSession::create($validated);

        return redirect()->route('admin.evoting.index')->with('success', 'Voting session created successfully!');
    }

    public function toggleSessionStatus(VotingSession $session)
    {
        $session->update(['is_active' => !$session->is_active]);
        return back()->with('success', 'Voting session status updated.');
    }

    public function storePosition(Request $request, VotingSession $session)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'integer|min:1',
        ]);

        $session->positions()->create($validated);

        return back()->with('success', 'Voting position added!');
    }

    public function storeCandidate(Request $request, VotingPosition $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manifesto' => 'nullable|string',
            'party_affiliation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('evoting/candidates', 'public');
        }

        $position->candidates()->create($validated);

        return back()->with('success', 'Candidate added successfully!');
    }

    public function results(VotingSession $session)
    {
        $session->load(['positions.candidates.votes']);

        return view('admin.evoting.results', compact('session'));
    }
}
