<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\VotingSession;
use App\Models\VotingPosition;
use App\Models\VotingCandidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EVotingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;
        $currentSem = $studentProfile?->current_semester ?? 1;

        // Active session matching semester or any active session
        $sessions = VotingSession::with(['positions.candidates.votes'])
            ->where('is_active', true)
            ->get();

        $activeSession = $sessions->firstWhere('target_semester', $currentSem) ?? $sessions->first();

        // Get user's existing votes
        $userVotes = Vote::where('user_id', $user->id)
            ->pluck('voting_candidate_id', 'voting_position_id')
            ->toArray();

        return view('student.evoting.index', compact('sessions', 'activeSession', 'userVotes', 'currentSem'));
    }

    public function announcements()
    {
        $sessions = VotingSession::where('is_active', true)->orderBy('created_at', 'desc')->get();
        return view('student.evoting.announcements', compact('sessions'));
    }

    public function positions()
    {
        $sessions = VotingSession::with(['positions.candidates'])->where('is_active', true)->get();
        return view('student.evoting.positions', compact('sessions'));
    }

    public function vote(Request $request)
    {
        $request->validate([
            'voting_session_id' => 'required|exists:voting_sessions,id',
            'voting_position_id' => 'required|exists:voting_positions,id',
            'voting_candidate_id' => 'required|exists:voting_candidates,id',
        ]);

        $user = Auth::user();

        // Check if user already voted for this position
        $existingVote = Vote::where('voting_session_id', $request->voting_session_id)
            ->where('voting_position_id', $request->voting_position_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            return back()->with('error', 'You have already voted for this position!');
        }

        Vote::create([
            'voting_session_id' => $request->voting_session_id,
            'voting_position_id' => $request->voting_position_id,
            'voting_candidate_id' => $request->voting_candidate_id,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Your vote has been cast successfully!');
    }

    public function applyCandidacy(Request $request)
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        if (!$profile || $profile->status !== 'active') {
            return back()->with('error', 'Only active students can apply for election candidacy.');
        }

        $request->validate([
            'voting_position_id' => 'required|exists:voting_positions,id',
            'manifesto' => 'required|string|max:2000',
            'party_affiliation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $position = VotingPosition::with('session')->findOrFail($request->voting_position_id);
        $session = $position->session;

        // Check if vetting period is open
        if (!$session || !$session->is_vetting_open) {
            return back()->with('error', 'Candidacy application & vetting window is currently closed for this election session.');
        }

        // Check if student already applied for this position
        $existing = VotingCandidate::where('voting_position_id', $position->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already submitted a candidacy application for this post.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('evoting/candidates', 'public');
        }

        VotingCandidate::create([
            'voting_position_id' => $position->id,
            'user_id' => $user->id,
            'name' => $user->full_name,
            'photo' => $photoPath,
            'manifesto' => $request->manifesto,
            'party_affiliation' => $request->party_affiliation ?? 'Independent',
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your candidacy application for ' . $position->title . ' has been submitted successfully and is pending vetting approval!');
    }
}
