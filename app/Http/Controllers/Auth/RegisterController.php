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
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except(['verifyEmail', 'resendVerification', 'getPendingApplications', 'processApplication']);
    }

    public function showRegistrationForm()
    {
        $departments = Department::where('is_active', true)->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        return view('auth.register', compact('departments', 'currentAcademicYear', 'currentSemester'));
    }

    public function register(Request $request)
    {
        // Custom validation based on role
        $validator = $this->getValidationRules($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Handle file uploads
            $profilePicture = null;
            $documents = [];

            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture')->store('profile-pictures', 'public');
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $key => $file) {
                    $documents[$key] = $file->store('student-documents', 'public');
                }
            }

            // Create user account
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact_name,
                'emergency_phone' => $request->emergency_contact_phone,
                'profile_picture' => $profilePicture,
                'is_active' => false, // Requires admin approval
                'email_verified_at' => null, // Requires email verification
            ]);

            // Create role-specific profile with enhanced data
            if ($request->role === 'student') {
                $this->createStudentProfile($user, $request, $documents);
            } elseif ($request->role === 'faculty') {
                $this->createFacultyProfile($user, $request, $documents);
            }

            DB::commit();

            // Send emails with better error handling
            $emailResults = [];

            // Send email verification
            $emailResults['verification'] = $this->sendEmailSafely(function() use ($user) {
                Mail::to($user->email)->send(new EmailVerification($user));
            }, 'Email verification', $user->email);

            // Send application submitted confirmation
            $emailResults['confirmation'] = $this->sendEmailSafely(function() use ($user) {
                Mail::to($user->email)->send(new ApplicationSubmitted($user));
            }, 'Application confirmation', $user->email);

            // Send notification to admins
            $emailResults['admin_notifications'] = $this->sendAdminNotifications($user);

            // Log email results
            Log::info('Registration completed for user: ' . $user->email, [
                'user_id' => $user->id,
                'email_results' => $emailResults
            ]);

            $message = 'Registration successful! Please check your email to verify your account. Your application is now under review.';

            // Add email status to success message if there were issues
            $failedEmails = array_filter($emailResults, function($result) {
                return !$result;
            });

            if (!empty($failedEmails)) {
                $message .= ' Note: Some email notifications may be delayed due to server issues.';
            }

            return redirect()->route('login')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration failed: ' . $e->getMessage(), [
                'email' => $request->email,
                'role' => $request->role,
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

        return redirect()->route('login')->with('success', 'Email verified successfully! Your application is now under review.');
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
        $baseRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:student,faculty',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:500',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ];

        if ($request->role === 'student') {
            $studentRules = [
                'program' => 'required|string|max:255',
                'specialization' => 'nullable|string|max:255',
                'guardian_name' => 'required|string|max:255',
                'guardian_phone' => 'required|string|max:20',
                'guardian_email' => 'nullable|email',
                'guardian_address' => 'required|string|max:500',
                'previous_school' => 'required|string|max:255',
                'previous_school_address' => 'nullable|string|max:500',
                'graduation_year' => 'required|integer|min:1990|max:' . date('Y'),
                'previous_gpa' => 'nullable|numeric|min:0|max:4',
                'sat_score' => 'nullable|integer|min:400|max:1600',
                'act_score' => 'nullable|integer|min:1|max:36',
                'toefl_score' => 'nullable|integer|min:0|max:120',
                'ielts_score' => 'nullable|numeric|min:0|max:9',
                'major_subjects' => 'nullable|string',
                'other_certifications' => 'nullable|string',
                'application_notes' => 'nullable|string|max:1000',
            ];
            $baseRules = array_merge($baseRules, $studentRules);
        }

        if ($request->role === 'faculty') {
            $facultyRules = [
                'position' => 'required|string|max:255',
                'highest_degree' => 'required|string|max:255',
                'degree_institution' => 'required|string|max:255',
                'degree_year' => 'required|integer|min:1970|max:' . date('Y'),
                'specialization' => 'required|string|max:255',
                'years_of_experience' => 'required|integer|min:0|max:50',
                'certifications' => 'nullable|string',
                'previous_positions' => 'nullable|string',
                'research_interests' => 'nullable|string',
                'application_notes' => 'nullable|string|max:1000',
            ];
            $baseRules = array_merge($baseRules, $facultyRules);
        }

        return Validator::make($request->all(), $baseRules);
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
