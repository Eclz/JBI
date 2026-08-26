<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\FacultyProfile;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Program;
use App\Models\SystemSetting;
use App\Mail\EmailVerification;
use App\Mail\ApplicationSubmitted;
use App\Mail\AdmissionApproved;
use App\Mail\ApplicationRejected;
use App\Mail\NewApplicationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except(['verifyEmail', 'resendVerification', 'getPendingApplications', 'processApplication']);
    }

    public function showRegistrationForm()
    {
        return view('auth.register', ['admissionWindow' => SystemSetting::admissionWindow()]);
    }

    public function register(Request $request)
    {
        if (!SystemSetting::admissionWindow()['isOpen']) {
            return back()->withErrors(['admission' => 'Admissions are currently closed. Please check the published opening dates or contact the admissions office.']);
        }

        // Custom validation
        $validator = $this->getValidationRules($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Handle file uploads
            $profilePicture = null;

            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture')->store('profile-pictures', 'public');
            }

            // Create user account as student
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'phone' => $request->phone,
                'is_active' => true, // Active to allow logging in to apply
                'email_verified_at' => null, // Requires email verification
            ]);

            DB::commit();

            // Create database notification for successful registration
            try {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Account Created Successfully',
                    'message' => 'Welcome to JBI University! Please complete your profile application to proceed.',
                    'type' => 'info',
                    'priority' => 'high',
                ]);
            } catch (\Exception $notifError) {
                Log::error('Failed to create registration notification: ' . $notifError->getMessage());
            }

            // Send email verification in the background
            $emailResults = [];
            $emailResults['verification'] = $this->sendEmailSafely(function() use ($user) {
                Mail::to($user->email)->send(new EmailVerification($user));
            }, 'Email verification', $user->email);

            // Log email results
            Log::info('Registration completed for user: ' . $user->email, [
                'user_id' => $user->id,
                'email_results' => $emailResults
            ]);

            // Authenticate and log in the user immediately
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Account created successfully! Please fill out your admission application below.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration failed: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    private function sendEmailSafely($emailFunction, $emailType, $recipient)
    {
        try {
            $emailFunction();
            Log::info("{$emailType} sent successfully to: {$recipient}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send {$emailType} to {$recipient}: " . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function verifyEmail(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->email))) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid verification link.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'Email already verified. You can now log in.');
        }

        $user->markEmailAsVerified();

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now log in and complete your admission application.');
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'Email is already verified.');
        }

        $success = $this->sendEmailSafely(function() use ($user) {
            Mail::to($user->email)->send(new EmailVerification($user));
        }, 'Verification email resend', $user->email);

        if ($success) {
            return back()->with('success', 'Verification email sent!');
        } else {
            return back()->with('error', 'Failed to send verification email. Please try again or contact support.');
        }
    }

    private function sendAdminNotifications($user)
    {
        try {
            // Get all active admin users
            $admins = User::where('role', 'admin')
                         ->where('is_active', true)
                         ->whereNotNull('email_verified_at')
                         ->get();

            $successCount = 0;
            $totalAdmins = $admins->count();

            foreach ($admins as $admin) {
                $success = $this->sendEmailSafely(function() use ($admin, $user) {
                    Mail::to($admin->email)->send(new NewApplicationNotification($user));
                }, 'Admin notification', $admin->email);

                if ($success) {
                    $successCount++;
                }

                // Create database notification for web UI
                try {
                    \App\Models\Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'application',
                        'title' => 'New User Registration',
                        'message' => "New registration from {$user->name} as {$user->role}",
                        'priority' => 'high',
                        'action_url' => route('admin.users.show', $user),
                    ]);
                } catch (\Exception $dbNotificationError) {
                    Log::error('Failed to create database notification: ' . $dbNotificationError->getMessage());
                }
            }

            if ($totalAdmins === 0) {
                Log::warning('No active admin users found to send application notifications');
                return false;
            }

            Log::info("Admin notifications: {$successCount}/{$totalAdmins} sent successfully");
            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Failed to send admin notifications: ' . $e->getMessage());
            return false;
        }
    }

    private function createStudentProfile($user, $request, $documents)
    {
        $admissionNumber = $this->generateAdmissionNumber();

        StudentProfile::create([
            'user_id' => $user->id,
            'admission_number' => $admissionNumber,
            'department_id' => $request->department_id,
            'program' => $request->program,
            'specialization' => $request->specialization,
            'admission_date' => now(),
            'status' => 'pending',
            'application_status' => 'submitted',
            'current_semester' => 1,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
            'guardian_address' => $request->guardian_address,
            'previous_school' => $request->previous_school,
            'previous_school_address' => $request->previous_school_address ?? '',
            'graduation_year' => $request->graduation_year,
            'previous_gpa' => $request->previous_gpa,
            'academic_history' => [
                'high_school' => [
                    'name' => $request->previous_school,
                    'address' => $request->previous_school_address ?? '',
                    'graduation_year' => $request->graduation_year,
                    'gpa' => $request->previous_gpa,
                    'major_subjects' => $request->major_subjects ? explode(',', $request->major_subjects) : [],
                ]
            ],
            'qualifications' => [
                'high_school_diploma' => $request->has('high_school_diploma'),
                'sat_score' => $request->sat_score,
                'act_score' => $request->act_score,
                'toefl_score' => $request->toefl_score,
                'ielts_score' => $request->ielts_score,
                'other_certifications' => $request->other_certifications ? explode(',', $request->other_certifications) : [],
            ],
            'documents' => $documents,
            'application_notes' => $request->application_notes,
            'expected_graduation_date' => $this->calculateExpectedGraduation($request->program),
            'notes' => 'Application submitted on ' . now()->format('Y-m-d H:i:s'),
        ]);
    }

    private function createFacultyProfile($user, $request, $documents)
    {
        $employeeId = $this->generateEmployeeId();

        FacultyProfile::create([
            'user_id' => $user->id,
            'employee_id' => $employeeId,
            'department_id' => $request->department_id,
            'position' => $request->position ?? 'Instructor',
            'hire_date' => now(),
            'employment_status' => 'pending',
            'application_status' => 'submitted',
            'qualifications' => [
                'highest_degree' => $request->highest_degree,
                'institution' => $request->degree_institution,
                'graduation_year' => $request->degree_year,
                'specialization' => $request->specialization,
                'certifications' => $request->certifications ? explode(',', $request->certifications) : [],
            ],
            'experience' => [
                'years_of_experience' => $request->years_of_experience,
                'previous_positions' => $request->previous_positions ? explode(',', $request->previous_positions) : [],
                'research_interests' => $request->research_interests ? explode(',', $request->research_interests) : [],
            ],
            'documents' => $documents,
            'application_notes' => $request->application_notes,
        ]);
    }

    private function getValidationRules($request)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string|max:20',
        ];

        return Validator::make($request->all(), $rules);
    }

    private function generateAdmissionNumber()
    {
        $year = date('Y');
        $lastStudent = StudentProfile::whereYear('created_at', $year)->count();
        return 'JBI' . $year . str_pad($lastStudent + 1, 4, '0', STR_PAD_LEFT);
    }

    private function generateEmployeeId()
    {
        $year = date('Y');
        $lastEmployee = FacultyProfile::whereYear('created_at', $year)->count();
        return 'EMP' . $year . str_pad($lastEmployee + 1, 4, '0', STR_PAD_LEFT);
    }

    private function calculateExpectedGraduation($program)
    {
        $programDurations = [
            'Bachelor of Arts' => 4,
            'Bachelor of Science' => 4,
            'Bachelor of Theology' => 4,
            'Master of Arts' => 2,
            'Master of Science' => 2,
            'Master of Divinity' => 3,
            'Doctor of Philosophy' => 4,
            'Doctor of Theology' => 4,
        ];

        $years = $programDurations[$program] ?? 4;
        return now()->addYears($years);
    }

    // API endpoint for admin to get pending applications
    public function getPendingApplications(Request $request)
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = User::with(['studentProfile', 'facultyProfile', 'studentProfile.department', 'facultyProfile.department'])
                    ->where('is_active', false);

        // Apply filters
        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->department_id) {
            $query->whereHas('studentProfile', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            })->orWhereHas('facultyProfile', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->application_status) {
            $query->whereHas('studentProfile', function($q) use ($request) {
                $q->where('application_status', $request->application_status);
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($applications);
    }

    // API endpoint for admin to approve/reject applications
    public function processApplication(Request $request, $userId)
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($userId);

            if ($request->action === 'approve') {
                // Generate default password
                $defaultPassword = User::generateJBIDefaultPassword();

                $user->update([
                    'is_active' => true,
                    'password' => $defaultPassword,
                    'default_password' => $defaultPassword,
                    'must_change_password' => true,
                ]);

                if ($user->studentProfile) {
                    $user->studentProfile->update([
                        'application_status' => 'approved',
                        'status' => 'active',
                        'notes' => ($user->studentProfile->notes ?? '') . "\nApproved on " . now()->format('Y-m-d H:i:s') . " by " . auth()->user()->name . ". " . ($request->notes ?? ''),
                    ]);
                }

                if ($user->facultyProfile) {
                    $user->facultyProfile->update([
                        'employment_status' => 'active',
                        'application_status' => 'approved',
                    ]);
                }

                // Send admission approval email with default password
                $emailSent = $this->sendEmailSafely(function() use ($user, $defaultPassword) {
                    Mail::to($user->email)->send(new AdmissionApproved($user, $defaultPassword));
                }, 'Admission approval', $user->email);

            } else {
                if ($user->studentProfile) {
                    $user->studentProfile->update([
                        'application_status' => 'rejected',
                        'notes' => ($user->studentProfile->notes ?? '') . "\nRejected on " . now()->format('Y-m-d H:i:s') . " by " . auth()->user()->name . ". " . ($request->notes ?? ''),
                    ]);
                }

                if ($user->facultyProfile) {
                    $user->facultyProfile->update([
                        'application_status' => 'rejected',
                    ]);
                }

                // Send rejection email
                $emailSent = $this->sendEmailSafely(function() use ($user, $request) {
                    Mail::to($user->email)->send(new ApplicationRejected($user, $request->notes));
                }, 'Application rejection', $user->email);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application ' . $request->action . 'd successfully.',
                'email_sent' => $emailSent ?? false
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to process application: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process application: ' . $e->getMessage()], 500);
        }
    }
}
