<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VotingSession;
use App\Models\VotingPosition;
use App\Models\VotingCandidate;
use App\Models\ElectoralCommissionMember;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\User;
use App\Models\Vote;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EVotingController extends Controller
{
    public function index(Request $request)
    {
        $query = VotingSession::with([
            'academicYear',
            'creator',
            'positions.faculty',
            'candidates',
            'commissionMembers.user',
            'votes'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sessions = $query->orderByDesc('created_at')->paginate(12);

        $academicYears = AcademicYear::orderByDesc('year')->get();
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();

        // High-level statistics
        $stats = [
            'total_elections' => VotingSession::count(),
            'active_voting' => VotingSession::where('status', 'voting_open')->count(),
            'total_candidates' => VotingCandidate::count(),
            'approved_candidates' => VotingCandidate::whereIn('candidate_status', ['approved_candidate', 'elected_student_leader'])->count(),
            'total_votes_cast' => Vote::count(),
            'elected_leaders' => VotingCandidate::where('candidate_status', 'elected_student_leader')->count(),
        ];

        return view('admin.evoting.index', compact('sessions', 'academicYears', 'faculties', 'stats'));
    }

    public function show(VotingSession $session)
    {
        $session->load([
            'academicYear',
            'creator',
            'positions.faculty',
            'positions.candidates.user.studentProfile.department.faculty',
            'positions.candidates.votes',
            'commissionMembers.user',
            'candidates.user.studentProfile.department.faculty',
            'candidates.position.faculty',
            'candidates.vetter',
            'votes.candidate',
            'votes.voter',
        ]);

        $academicYears = AcademicYear::orderByDesc('year')->get();
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $eligibleCommissionUsers = User::where('is_active', true)
            ->whereNotIn('id', $session->commissionMembers->pluck('user_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $totalEligibleStudents = User::where('role', 'student')->where('is_active', true)->count();
        $totalVotersCount = $session->votes()->distinct('user_id')->count('user_id');
        $turnoutPercentage = $totalEligibleStudents > 0 
            ? round(($totalVotersCount / $totalEligibleStudents) * 100, 1) 
            : 0;

        return view('admin.evoting.show', compact(
            'session',
            'academicYears',
            'faculties',
            'eligibleCommissionUsers',
            'totalEligibleStudents',
            'totalVotersCount',
            'turnoutPercentage'
        ));
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_semester' => 'required|integer|in:1,2',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'application_start_at' => 'nullable|date',
            'application_end_at' => 'nullable|date|after_or_equal:application_start_at',
            'vetting_start_at' => 'nullable|date',
            'vetting_end_at' => 'nullable|date|after_or_equal:vetting_start_at',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'status' => ['required', Rule::in([
                'draft', 'applications_open', 'vetting', 'voting_scheduled',
                'voting_open', 'voting_closed', 'results_under_review',
                'results_published', 'completed'
            ])],
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();

        $session = VotingSession::create($validated);

        // Auto-assign creator to Electoral Commission as Chairperson if not already
        ElectoralCommissionMember::create([
            'voting_session_id' => $session->id,
            'user_id' => auth()->id(),
            'role_title' => 'Electoral Chairperson',
            'appointed_at' => now(),
            'notes' => 'Primary Administrator & Electoral Lead',
            'is_active' => true,
        ]);

        return redirect()->route('admin.evoting.show', $session)
            ->with('success', 'Election Season created successfully! Now define the positions and commission members.');
    }

    public function updateSession(Request $request, VotingSession $session)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_semester' => 'required|integer|in:1,2',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'application_start_at' => 'nullable|date',
            'application_end_at' => 'nullable|date|after_or_equal:application_start_at',
            'vetting_start_at' => 'nullable|date',
            'vetting_end_at' => 'nullable|date|after_or_equal:vetting_start_at',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'status' => ['required', Rule::in([
                'draft', 'applications_open', 'vetting', 'voting_scheduled',
                'voting_open', 'voting_closed', 'results_under_review',
                'results_published', 'completed'
            ])],
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $session->update($validated);

        return back()->with('success', 'Election season details updated successfully.');
    }

    public function updateSessionStatus(Request $request, VotingSession $session)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'draft', 'applications_open', 'vetting', 'voting_scheduled',
                'voting_open', 'voting_closed', 'results_under_review',
                'results_published', 'completed'
            ])],
        ]);

        $newStatus = $validated['status'];

        // If publishing results, automatically promote winners to Elected Student Leaders
        if ($newStatus === 'results_published' && $session->status !== 'results_published') {
            $this->processElectedLeaders($session);
            $session->results_published_at = now();
        }

        $session->status = $newStatus;
        $session->save();

        return back()->with('success', "Election status transitioned to: {$session->status_label}");
    }

    public function destroySession(VotingSession $session)
    {
        $session->delete();
        return redirect()->route('admin.evoting.index')
            ->with('success', 'Election session deleted successfully.');
    }

    public function storePosition(Request $request, VotingSession $session)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => 'required|in:university_wide,faculty_specific',
            'faculty_id' => 'nullable|required_if:scope,faculty_specific|exists:faculties,id',
            'max_votes_per_voter' => 'required|integer|min:1|max:5',
            'requirements' => 'nullable|string|max:1000',
            'display_order' => 'required|integer|min:1',
        ]);

        if ($validated['scope'] === 'university_wide') {
            $validated['faculty_id'] = null;
        }

        $session->positions()->create($validated);

        return back()->with('success', 'Electoral position added successfully.');
    }

    public function updatePosition(Request $request, VotingPosition $position)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => 'required|in:university_wide,faculty_specific',
            'faculty_id' => 'nullable|required_if:scope,faculty_specific|exists:faculties,id',
            'max_votes_per_voter' => 'required|integer|min:1|max:5',
            'requirements' => 'nullable|string|max:1000',
            'display_order' => 'required|integer|min:1',
        ]);

        if ($validated['scope'] === 'university_wide') {
            $validated['faculty_id'] = null;
        }

        $position->update($validated);

        return back()->with('success', 'Electoral position updated successfully.');
    }

    public function destroyPosition(VotingPosition $position)
    {
        $position->delete();
        return back()->with('success', 'Electoral position removed.');
    }

    public function storeCommissionMember(Request $request, VotingSession $session)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_title' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $exists = $session->commissionMembers()->where('user_id', $validated['user_id'])->exists();
        if ($exists) {
            return back()->with('error', 'This user is already a member of the Electoral Commission.');
        }

        $session->commissionMembers()->create([
            'user_id' => $validated['user_id'],
            'role_title' => $validated['role_title'],
            'appointed_at' => now(),
            'notes' => $validated['notes'],
            'is_active' => true,
        ]);

        Notification::create([
            'user_id' => $validated['user_id'],
            'type' => 'info',
            'title' => 'Appointed to Electoral Commission',
            'message' => "You have been officially appointed as {$validated['role_title']} for {$session->title}.",
            'priority' => 'high',
        ]);

        return back()->with('success', 'Electoral Commission member appointed successfully.');
    }

    public function destroyCommissionMember(VotingSession $session, ElectoralCommissionMember $member)
    {
        $member->delete();
        return back()->with('success', 'Commission member removed.');
    }

    public function vetCandidate(Request $request, VotingCandidate $candidate)
    {
        $validated = $request->validate([
            'application_status' => 'required|in:submitted,under_review,vetted_approved,rejected,withdrawn',
            'vetting_score' => 'nullable|numeric|min:0|max:100',
            'vetting_notes' => 'nullable|string|max:2000',
        ]);

        $appStatus = $validated['application_status'];
        $candidateStatus = match ($appStatus) {
            'vetted_approved' => 'approved_candidate',
            'rejected' => 'not_elected',
            default => 'applicant',
        };

        $candidate->update([
            'application_status' => $appStatus,
            'candidate_status' => $candidateStatus,
            'status' => ($appStatus === 'vetted_approved') ? 'approved' : 'rejected',
            'vetting_score' => $validated['vetting_score'] ?? $candidate->vetting_score,
            'vetting_notes' => $validated['vetting_notes'] ?? null,
            'vetted_by' => auth()->id(),
            'vetted_at' => now(),
        ]);

        // Send notification to applicant student
        if ($candidate->user_id) {
            $msg = match ($appStatus) {
                'vetted_approved' => "Congratulations! Your candidacy for '{$candidate->position->title}' has been vetted and APPROVED. You will appear on the official ballot.",
                'rejected' => "Your candidacy application for '{$candidate->position->title}' was rejected during vetting. Reason: " . ($validated['vetting_notes'] ?? 'Requirements not met.'),
                'under_review' => "Your candidacy application for '{$candidate->position->title}' is currently under detailed review by the Electoral Commission.",
                default => "Your candidacy status has been updated.",
            };

            Notification::create([
                'user_id' => $candidate->user_id,
                'type' => ($appStatus === 'vetted_approved') ? 'success' : (($appStatus === 'rejected') ? 'danger' : 'warning'),
                'title' => 'Candidacy Vetting Update',
                'message' => $msg,
                'priority' => 'high',
            ]);
        }

        return back()->with('success', "Candidate application vetted and marked as: " . ucfirst(str_replace('_', ' ', $appStatus)));
    }

    public function publishResults(Request $request, VotingSession $session)
    {
        DB::beginTransaction();
        try {
            $this->processElectedLeaders($session);
            $session->update([
                'status' => 'results_published',
                'results_published_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Official election results published! Winning candidates have been promoted to Elected Student Leaders.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to publish results: ' . $e->getMessage());
        }
    }

    public function leadersIndex()
    {
        $leaders = VotingCandidate::where('candidate_status', 'elected_student_leader')
            ->with(['position.session', 'user.studentProfile.department.faculty'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.evoting.leaders', compact('leaders'));
    }

    public function results(VotingSession $session)
    {
        $session->load([
            'academicYear',
            'positions.faculty',
            'positions.candidates.votes',
            'positions.candidates.user',
            'votes',
        ]);

        $totalEligibleStudents = User::where('role', 'student')->where('is_active', true)->count();
        $totalVotersCount = $session->votes()->distinct('user_id')->count('user_id');

        return view('admin.evoting.results', compact('session', 'totalEligibleStudents', 'totalVotersCount'));
    }

    private function processElectedLeaders(VotingSession $session): void
    {
        foreach ($session->positions as $position) {
            $candidates = $position->candidates()
                ->whereIn('candidate_status', ['approved_candidate', 'elected_student_leader'])
                ->withCount('votes')
                ->orderByDesc('votes_count')
                ->get();

            if ($candidates->isEmpty()) {
                continue;
            }

            // The highest vote getter wins the position
            $maxVotes = $candidates->first()->votes_count;
            $isFirst = true;

            foreach ($candidates as $candidate) {
                // If there are votes and candidate has top votes
                if ($isFirst && $candidate->votes_count > 0) {
                    $candidate->update(['candidate_status' => 'elected_student_leader']);
                    $isFirst = false;

                    if ($candidate->user_id) {
                        Notification::create([
                            'user_id' => $candidate->user_id,
                            'type' => 'success',
                            'title' => 'Congratulations - Elected Student Leader!',
                            'message' => "You have been elected as {$position->title} in {$session->title}!",
                            'priority' => 'high',
                        ]);
                    }
                } else {
                    $candidate->update(['candidate_status' => 'not_elected']);
                }
            }
        }
    }
}
