<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\VotingSession;
use App\Models\VotingPosition;
use App\Models\VotingCandidate;
use App\Models\Vote;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EVotingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;
        $facultyId = $studentProfile?->department?->faculty_id;

        // Ongoing, application-open, or upcoming elections
        $sessions = VotingSession::with([
            'academicYear',
            'positions.faculty',
            'positions.approvedCandidates',
            'votes'
        ])
        ->where('is_active', true)
        ->orderByDesc('created_at')
        ->get();

        // User's applications
        $myApplications = VotingCandidate::where('user_id', $user->id)
            ->with(['position.session', 'position.faculty'])
            ->orderByDesc('created_at')
            ->get();

        // User's cast votes
        $userVotes = Vote::where('user_id', $user->id)
            ->pluck('voting_candidate_id', 'voting_position_id')
            ->toArray();

        // Elected Student Leaders
        $electedLeaders = VotingCandidate::where('candidate_status', 'elected_student_leader')
            ->with(['position.session', 'position.faculty', 'user.studentProfile.department.faculty'])
            ->orderBy('position_id')
            ->get();

        return view('student.evoting.index', compact(
            'sessions',
            'myApplications',
            'userVotes',
            'electedLeaders',
            'studentProfile',
            'facultyId'
        ));
    }

    public function apply(VotingSession $session)
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;

        if (!$studentProfile || $studentProfile->status !== 'active') {
            return redirect()->route('student.evoting.index')
                ->with('error', 'Only fully active students can submit applications for student leadership positions.');
        }

        $facultyId = $studentProfile->department?->faculty_id;

        // Load positions available to this student: university-wide OR matching student's faculty
        $eligiblePositions = $session->positions()
            ->where(function ($q) use ($facultyId) {
                $q->where('scope', 'university_wide');
                if ($facultyId) {
                    $q->orWhere('faculty_id', $facultyId);
                }
            })
            ->with('faculty')
            ->orderBy('display_order')
            ->get();

        $hasRetake = $this->checkStudentHasRetake($user->id);
        $existingApplications = VotingCandidate::where('voting_session_id', $session->id)
            ->where('user_id', $user->id)
            ->pluck('voting_position_id')
            ->toArray();

        return view('student.evoting.apply', compact('session', 'eligiblePositions', 'studentProfile', 'existingApplications', 'hasRetake'));
    }

    public function storeApplication(Request $request, VotingSession $session)
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;

        if (!$studentProfile || $studentProfile->status !== 'active') {
            return back()->with('error', 'Only active students are eligible to apply.');
        }

        // Check if application period is open
        if (!$session->is_application_open && $session->status !== 'applications_open') {
            return back()->with('error', 'The candidate application window is currently closed for this election season.');
        }

        $validated = $request->validate([
            'voting_position_id' => 'required|exists:voting_positions,id',
            'slogan' => 'nullable|string|max:255',
            'manifesto' => 'required|string|max:5000',
            'party_affiliation' => 'nullable|string|max:255',
            'cgpa' => 'nullable|numeric|min:0|max:5',
            'year_of_study' => 'nullable|integer|min:1|max:7',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $position = VotingPosition::where('voting_session_id', $session->id)
            ->findOrFail($validated['voting_position_id']);

        // Check faculty restriction
        if ($position->scope === 'faculty_specific' && $position->faculty_id) {
            $studentFacultyId = $studentProfile->department?->faculty_id;
            if ((int) $position->faculty_id !== (int) $studentFacultyId) {
                return back()->with('error', 'You can only apply for positions within your own faculty or university-wide positions.');
            }
        }

        // Check duplicate application
        $exists = VotingCandidate::where('voting_position_id', $position->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already submitted an application for this position.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('evoting/candidates', 'public');
        } elseif ($user->profile_picture) {
            $photoPath = $user->profile_picture;
        }

        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $documents[] = [
                    'name' => $doc->getClientOriginalName(),
                    'path' => $doc->store('evoting/documents/' . $session->id, 'public'),
                ];
            }
        }

        // Automated Academic Vetting Check (Course retake / failing grade check)
        $hasRetake = $this->checkStudentHasRetake($user->id);

        $candidate = VotingCandidate::create([
            'voting_session_id' => $session->id,
            'voting_position_id' => $position->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'slogan' => $validated['slogan'] ?? null,
            'manifesto' => $validated['manifesto'],
            'party_affiliation' => $validated['party_affiliation'] ?? null,
            'photo' => $photoPath,
            'cgpa' => $validated['cgpa'] ?? null,
            'year_of_study' => $validated['year_of_study'] ?? ($studentProfile->year_of_study ?? 1),
            'faculty_id' => $studentProfile->department?->faculty_id,
            'supporting_documents' => $documents,
            'application_status' => $hasRetake ? 'rejected' : 'submitted',
            'candidate_status' => $hasRetake ? 'disqualified' : 'applicant',
            'status' => $hasRetake ? 'rejected' : 'pending',
            'vetting_notes' => $hasRetake 
                ? 'Automated Academic Vetting Engine: Disqualified due to an active course retake, failed course, or retake invoice on your academic record.'
                : null,
        ]);

        if ($hasRetake) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'warning',
                'title' => 'Electoral Candidacy Vetting Outcome - Disqualified',
                'message' => "Your candidacy application for {$position->title} in {$session->title} was automatically vetted out. According to JBI Electoral Regulations, students with an active course retake or failing grade are ineligible to contest for student leadership positions.",
                'priority' => 'high',
                'action_url' => route('student.evoting.my-applications'),
            ]);

            return redirect()->route('student.evoting.my-applications')
                ->with('error', "Application Vetted Out: Your application for {$position->title} was automatically vetted out and disqualified because an active course retake or failed course was detected on your academic record.");
        }

        // Notify Admins & Electoral Commission if clean
        $commissionUsers = $session->commissionMembers()->with('user')->get();
        foreach ($commissionUsers as $commMember) {
            if ($commMember->user) {
                Notification::create([
                    'user_id' => $commMember->user->id,
                    'type' => 'info',
                    'title' => 'New Candidate Application Submitted',
                    'message' => "{$user->name} applied for {$position->title} in {$session->title}. Academic vetting passed; ready for review.",
                    'priority' => 'high',
                ]);
            }
        }

        return redirect()->route('student.evoting.my-applications')
            ->with('success', 'Your candidacy application has been submitted successfully! Academic check passed; the Electoral Commission will review your portfolio.');
    }

    private function checkStudentHasRetake($userId)
    {
        $hasRetakeEnrollment = \App\Models\CourseEnrollment::where('user_id', $userId)
            ->where(function($q) {
                $q->where('enrollment_type', 'retake')
                  ->orWhere('status', 'failed')
                  ->orWhere('letter_grade', 'F');
            })->exists();

        $hasFailingGrade = \App\Models\Grade::where('user_id', $userId)
            ->where(function($q) {
                $q->where('letter_grade', 'F')
                  ->orWhere('percentage', '<', 50);
            })->exists();

        $hasRetakeFee = \App\Models\Fee::where('user_id', $userId)
            ->whereIn('type', ['retake', 'retake_fee', 'missed_paper'])
            ->exists();

        return $hasRetakeEnrollment || $hasFailingGrade || $hasRetakeFee;
    }

    public function myApplications()
    {
        $user = Auth::user();
        $applications = VotingCandidate::where('user_id', $user->id)
            ->with(['position.session', 'position.faculty', 'vetter'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.evoting.my-applications', compact('applications'));
    }

    public function ballot(VotingSession $session)
    {
        $user = Auth::user();

        // Enforce student role
        if ($user->role !== 'student') {
            return redirect()->route('dashboard')->with('error', 'Only registered students are eligible to vote in student elections.');
        }

        $studentProfile = $user->studentProfile;
        $facultyId = $studentProfile?->department?->faculty_id;

        // Check if voting is open
        if (!$session->is_voting_open) {
            return redirect()->route('student.evoting.index')
                ->with('error', 'Voting is not currently open for this election session.');
        }

        // Load positions available to this student: university_wide or student's faculty
        $positions = $session->positions()
            ->where(function ($q) use ($facultyId) {
                $q->where('scope', 'university_wide');
                if ($facultyId) {
                    $q->orWhere('faculty_id', $facultyId);
                }
            })
            ->with([
                'faculty',
                'approvedCandidates' => function ($q) {
                    $q->with('user');
                }
            ])
            ->orderBy('display_order')
            ->get();

        // Get student's cast votes in this session
        $myVotes = Vote::where('voting_session_id', $session->id)
            ->where('user_id', $user->id)
            ->pluck('voting_candidate_id', 'voting_position_id')
            ->toArray();

        return view('student.evoting.ballot', compact('session', 'positions', 'myVotes', 'studentProfile'));
    }

    public function castVote(Request $request, VotingSession $session)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            return response()->json(['success' => false, 'message' => 'Only students are permitted to cast votes.'], 403);
        }

        if (!$session->is_voting_open) {
            return response()->json(['success' => false, 'message' => 'Voting session is closed or not active.'], 422);
        }

        $validated = $request->validate([
            'voting_position_id' => 'required|exists:voting_positions,id',
            'voting_candidate_id' => 'required|exists:voting_candidates,id',
        ]);

        $position = VotingPosition::where('voting_session_id', $session->id)
            ->findOrFail($validated['voting_position_id']);

        // Check faculty restriction
        $studentProfile = $user->studentProfile;
        $studentFacultyId = $studentProfile?->department?->faculty_id;

        if ($position->scope === 'faculty_specific' && $position->faculty_id) {
            if ((int) $position->faculty_id !== (int) $studentFacultyId) {
                return response()->json(['success' => false, 'message' => 'You are not eligible to vote for this faculty-specific position.'], 403);
            }
        }

        // Verify candidate belongs to position and is approved
        $candidate = VotingCandidate::where('voting_position_id', $position->id)
            ->where(function ($q) {
                $q->where('candidate_status', 'approved_candidate')
                  ->orWhere('candidate_status', 'elected_student_leader')
                  ->orWhere('application_status', 'vetted_approved')
                  ->orWhere('status', 'approved');
            })
            ->findOrFail($validated['voting_candidate_id']);

        // Prevent duplicate vote
        $alreadyVoted = Vote::where('voting_session_id', $session->id)
            ->where('voting_position_id', $position->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyVoted) {
            return response()->json(['success' => false, 'message' => 'You have already cast your vote for this position.'], 422);
        }

        DB::beginTransaction();
        try {
            $verificationHash = hash('sha256', $user->id . '-' . $position->id . '-' . $candidate->id . '-' . now()->timestamp . '-' . Str::random(8));

            Vote::create([
                'voting_session_id' => $session->id,
                'voting_position_id' => $position->id,
                'voting_candidate_id' => $candidate->id,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
            ]);

            $candidate->increment('votes_count');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Your vote for {$position->title} has been securely recorded!",
                'position_id' => $position->id,
                'candidate_name' => $candidate->name,
                'token' => substr($verificationHash, 0, 16),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to cast vote: ' . $e->getMessage()], 500);
        }
    }

    public function results(VotingSession $session)
    {
        if (!$session->is_results_published && !Auth::user()->isAdmin() && !Auth::user()->isElectoralCommissioner($session)) {
            return redirect()->route('student.evoting.index')
                ->with('error', 'Election results have not yet been published by the Electoral Commission.');
        }

        $session->load([
            'academicYear',
            'positions.faculty',
            'positions.candidates.votes',
            'positions.candidates.user',
            'votes',
        ]);

        $totalEligibleStudents = User::where('role', 'student')->where('is_active', true)->count();
        $totalVotersCount = $session->votes()->distinct('user_id')->count('user_id');

        return view('student.evoting.results', compact('session', 'totalEligibleStudents', 'totalVotersCount'));
    }

    public function leaders()
    {
        $leaders = VotingCandidate::where('candidate_status', 'elected_student_leader')
            ->with(['position.session.academicYear', 'position.faculty', 'user.studentProfile.department.faculty'])
            ->orderByDesc('updated_at')
            ->get();

        return view('student.evoting.leaders', compact('leaders'));
    }
}
