<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FacultyProfile;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacultyStaffController extends Controller
{
    /**
     * Display a listing of faculty staff members.
     */
    public function index(Request $request)
    {
        $query = User::with(['facultyProfile.department.faculty'])
            ->where('role', 'faculty');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('facultyProfile', function ($fq) use ($search) {
                      $fq->where('employee_id', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('specialization', 'like', "%{$search}%");
                  });
            });
        }

        // Faculty filter
        if ($request->filled('faculty')) {
            $query->whereHas('facultyProfile.department', function ($q) use ($request) {
                $q->where('faculty_id', $request->faculty);
            });
        }

        // Department filter
        if ($request->filled('department')) {
            $query->whereHas('facultyProfile', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Employment status filter
        if ($request->filled('employment_status')) {
            $query->whereHas('facultyProfile', function ($q) use ($request) {
                $q->where('employment_status', $request->employment_status);
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        if ($sortBy === 'name') {
            $query->orderBy('first_name', $sortOrder)->orderBy('last_name', $sortOrder);
        } elseif ($sortBy === 'department') {
            $query->join('faculty_profiles', 'users.id', '=', 'faculty_profiles.user_id')
                  ->join('departments', 'faculty_profiles.department_id', '=', 'departments.id')
                  ->orderBy('departments.name', $sortOrder)
                  ->select('users.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $facultyStaff = $query->paginate(20)->withQueryString();

        // Get faculties and departments for filter dropdowns
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->with('faculty')->orderBy('name')->get();

        // Calculate statistics
        $stats = [
            'total' => User::where('role', 'faculty')->count(),
            'active' => User::where('role', 'faculty')->where('is_active', true)->count(),
            'inactive' => User::where('role', 'faculty')->where('is_active', false)->count(),
            'full_time' => FacultyProfile::where('employment_type', 'full_time')->count(),
            'part_time' => FacultyProfile::where('employment_type', 'part_time')->count(),
        ];

        return view('admin.faculty-staff.index', compact('facultyStaff', 'faculties', 'departments', 'stats'));
    }

    /**
     * Show the form for creating a new faculty staff member.
     */
    public function create()
    {
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->with('faculty')->orderBy('name')->get();

        if ($departments->isEmpty()) {
            return redirect()->route('admin.faculty-staff.index')
                           ->with('error', 'No active departments available. Please create a department first.');
        }

        return view('admin.faculty-staff.create', compact('faculties', 'departments'));
    }

    /**
     * Store a newly created faculty staff member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:30',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,visiting',
            'highest_degree' => 'required|string|max:255',
            'degree_institution' => 'required|string|max:255',
            'degree_year' => 'required|integer|min:1970|max:' . date('Y'),
            'specialization' => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bio' => 'nullable|string|max:1000',
            'linkedin_profile' => 'nullable|url|max:255',
            'personal_website' => 'nullable|url|max:255',
            'certifications' => 'nullable|string|max:500',
            'research_interests' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Handle profile picture upload
            $profilePicture = null;
            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture')->store('profile-pictures', 'public');
            }

            // Generate default password
            $defaultPassword = $this->generateDefaultPassword();

            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($defaultPassword),
                'role' => 'faculty',
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'profile_picture' => $profilePicture,
                'is_active' => true,
                'email_verified_at' => now(),
                'must_change_password' => true,
            ]);

            // Generate employee ID
            $employeeId = $this->generateEmployeeId();

            // Create faculty profile
            FacultyProfile::create([
                'user_id' => $user->id,
                'employee_id' => $employeeId,
                'department_id' => $request->department_id,
                'designation' => $request->position,
                'position' => $request->position,
                'qualification' => $request->highest_degree,
                'specialization' => $request->specialization,
                'joining_date' => now(),
                'hire_date' => now(),
                'employment_type' => $request->employment_type,
                'employment_status' => 'active',
                'application_status' => 'approved',
                'years_of_experience' => $request->years_of_experience,
                'status' => 'active',
                'bio' => $request->bio,
                'linkedin_profile' => $request->linkedin_profile,
                'personal_website' => $request->personal_website,
                'qualifications' => [
                    'highest_degree' => $request->highest_degree,
                    'institution' => $request->degree_institution,
                    'graduation_year' => $request->degree_year,
                    'specialization' => $request->specialization,
                    'certifications' => $request->certifications ? array_map('trim', explode(',', $request->certifications)) : [],
                ],
                'experience' => [
                    'years_of_experience' => $request->years_of_experience,
                    'research_interests' => $request->research_interests ? array_map('trim', explode(',', $request->research_interests)) : [],
                ],
            ]);

            DB::commit();

            Log::info('Faculty staff member created successfully', [
                'user_id' => $user->id,
                'employee_id' => $employeeId,
                'created_by' => auth()->id()
            ]);

            return redirect()->route('admin.faculty-staff.index')
                           ->with('success', "Faculty staff member created successfully! Employee ID: {$employeeId}. Default password: {$defaultPassword}");

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if exists
            if ($profilePicture) {
                Storage::disk('public')->delete($profilePicture);
            }

            Log::error('Failed to create faculty staff member', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password', 'profile_picture'])
            ]);

            return back()->withErrors(['error' => 'Failed to create faculty staff member: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified faculty staff member.
     */
    public function show(User $facultyStaff)
    {
        if ($facultyStaff->role !== 'faculty' && !$facultyStaff->facultyProfile) {
            return redirect()->route('admin.faculty-staff.index')
                           ->with('error', 'User is not a faculty member.');
        }

        // If user has faculty profile but role is not set correctly, fix it
        if ($facultyStaff->facultyProfile && $facultyStaff->role !== 'faculty') {
            $facultyStaff->update(['role' => 'faculty']);
        }

        $facultyStaff->load([
            'facultyProfile.department.faculty',
            'taughtCourses.semester',
            'taughtCourses.enrollments',
            'taughtCourses.department'
        ]);

        // Ensure faculty profile exists
        if (!$facultyStaff->facultyProfile) {
            $this->createDefaultFacultyProfile($facultyStaff);
            $facultyStaff->load('facultyProfile.department.faculty');
        }

        // Get teaching statistics
        $teachingStats = [
            'total_courses' => $facultyStaff->taughtCourses->count(),
            'active_courses' => $facultyStaff->taughtCourses->where('is_active', true)->count(),
            'total_students' => $facultyStaff->taughtCourses->sum(function ($course) {
                return $course->enrollments->count();
            }),
            'current_semester_courses' => $facultyStaff->taughtCourses->filter(function ($course) {
                return $course->semester && $course->semester->is_current;
            })->count(),
        ];

        return view('admin.faculty-staff.show', compact('facultyStaff', 'teachingStats'));
    }

    /**
     * Show the form for editing the specified faculty staff member.
     */
    public function edit(User $facultyStaff)
    {
        if ($facultyStaff->role !== 'faculty' && !$facultyStaff->facultyProfile) {
            return redirect()->route('admin.faculty-staff.index')
                           ->with('error', 'User is not a faculty member.');
        }

        // If user has faculty profile but role is not set correctly, fix it
        if ($facultyStaff->facultyProfile && $facultyStaff->role !== 'faculty') {
            $facultyStaff->update(['role' => 'faculty']);
        }

        $facultyStaff->load('facultyProfile.department.faculty');

        // Ensure faculty profile exists
        if (!$facultyStaff->facultyProfile) {
            $this->createDefaultFacultyProfile($facultyStaff);
            $facultyStaff->load('facultyProfile.department.faculty');
        }

        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->with('faculty')->orderBy('name')->get();
        $courses = \App\Models\Course::with(['department', 'semester'])->orderBy('name')->get();
        $assignedCourseIds = $facultyStaff->taughtCourses->pluck('id')->toArray();

        return view('admin.faculty-staff.edit', compact('facultyStaff', 'faculties', 'departments', 'courses', 'assignedCourseIds'));
    }

    /**
     * Update the specified faculty staff member.
     */
    public function update(Request $request, User $facultyStaff)
    {
        if ($facultyStaff->role !== 'faculty' && !$facultyStaff->facultyProfile) {
            return redirect()->route('admin.faculty-staff.index')
                           ->with('error', 'User is not a faculty member.');
        }

        // If user has faculty profile but role is not set correctly, fix it
        if ($facultyStaff->facultyProfile && $facultyStaff->role !== 'faculty') {
            $facultyStaff->update(['role' => 'faculty']);
        }

        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $facultyStaff->id,
            'phone' => 'nullable|string|max:30',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:30',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,visiting',
            'employment_status' => 'required|in:active,inactive,on_leave,terminated',
            'highest_degree' => 'required|string|max:255',
            'degree_institution' => 'required|string|max:255',
            'degree_year' => 'required|integer|min:1970|max:' . date('Y'),
            'specialization' => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bio' => 'nullable|string|max:1000',
            'linkedin_profile' => 'nullable|url|max:255',
            'personal_website' => 'nullable|url|max:255',
            'certifications' => 'nullable|string|max:500',
            'research_interests' => 'nullable|string|max:500',
            'password' => 'nullable|min:8|confirmed',
            'is_active' => 'boolean',
            'assigned_courses' => 'nullable|array',
            'assigned_courses.*' => 'exists:courses,id',
        ]);

        DB::beginTransaction();

        try {
            // Handle profile picture upload
            $profilePicture = $facultyStaff->profile_picture;
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture
                if ($profilePicture) {
                    Storage::disk('public')->delete($profilePicture);
                }
                $profilePicture = $request->file('profile_picture')->store('profile-pictures', 'public');
            }

            // Update user data
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'profile_picture' => $profilePicture,
                'is_active' => $request->has('is_active'),
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
                $userData['must_change_password'] = false;
                $userData['password_changed_at'] = now();
            }

            $facultyStaff->update($userData);

            // Ensure faculty profile exists
            if (!$facultyStaff->facultyProfile) {
                $this->createDefaultFacultyProfile($facultyStaff);
            }

            // Update faculty profile
            $facultyStaff->facultyProfile->update([
                'department_id' => $request->department_id,
                'designation' => $request->position,
                'position' => $request->position,
                'qualification' => $request->highest_degree,
                'specialization' => $request->specialization,
                'employment_type' => $request->employment_type,
                'employment_status' => $request->employment_status,
                'years_of_experience' => $request->years_of_experience,
                'bio' => $request->bio,
                'linkedin_profile' => $request->linkedin_profile,
                'personal_website' => $request->personal_website,
                'qualifications' => [
                    'highest_degree' => $request->highest_degree,
                    'institution' => $request->degree_institution,
                    'graduation_year' => $request->degree_year,
                    'specialization' => $request->specialization,
                    'certifications' => $request->certifications ? array_map('trim', explode(',', $request->certifications)) : [],
                ],
                'experience' => [
                    'years_of_experience' => $request->years_of_experience,
                    'research_interests' => $request->research_interests ? array_map('trim', explode(',', $request->research_interests)) : [],
                ],
            ]);

            // Sync assigned courses
            if ($request->has('assigned_courses')) {
                $assignedIds = array_filter($request->input('assigned_courses', []));
                \App\Models\Course::whereIn('id', $assignedIds)->update(['instructor_id' => $facultyStaff->id]);
                \App\Models\Course::where('instructor_id', $facultyStaff->id)
                    ->whereNotIn('id', $assignedIds)
                    ->update(['instructor_id' => null]);
            }

            DB::commit();

            Log::info('Faculty staff member updated successfully', [
                'user_id' => $facultyStaff->id,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('admin.faculty-staff.index')
                           ->with('success', 'Faculty staff member updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update faculty staff member', [
                'user_id' => $facultyStaff->id,
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password', 'profile_picture'])
            ]);

            return back()->withErrors(['error' => 'Failed to update faculty staff member: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified faculty staff member.
     */
    public function destroy(User $facultyStaff)
    {
        if ($facultyStaff->role !== 'faculty') {
            return redirect()->route('admin.faculty-staff.index')
                           ->with('error', 'User is not a faculty member.');
        }

        // Check if faculty has active courses
        $activeCourses = $facultyStaff->taughtCourses()->where('is_active', true)->count();
        if ($activeCourses > 0) {
            return back()->with('error', 'Cannot delete faculty member with active courses. Please reassign courses first.');
        }

        DB::beginTransaction();

        try {
            // Delete profile picture if exists
            if ($facultyStaff->profile_picture) {
                Storage::disk('public')->delete($facultyStaff->profile_picture);
            }

            $employeeId = $facultyStaff->facultyProfile?->employee_id;

            // Delete faculty profile and user
            if ($facultyStaff->facultyProfile) {
                $facultyStaff->facultyProfile->delete();
            }
            $facultyStaff->delete();

            DB::commit();

            Log::info('Faculty staff member deleted successfully', [
                'user_id' => $facultyStaff->id,
                'employee_id' => $employeeId,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('admin.faculty-staff.index')
                           ->with('success', 'Faculty staff member deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete faculty staff member', [
                'user_id' => $facultyStaff->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete faculty staff member: ' . $e->getMessage());
        }
    }

    /**
     * Toggle faculty staff status via AJAX.
     */
    public function toggleStatus(User $facultyStaff)
    {
        if ($facultyStaff->role !== 'faculty') {
            return response()->json(['error' => 'User is not a faculty member.'], 400);
        }

        try {
            $facultyStaff->update(['is_active' => !$facultyStaff->is_active]);

            // Also update employment status in faculty profile
            if ($facultyStaff->facultyProfile) {
                $facultyStaff->facultyProfile->update([
                    'employment_status' => $facultyStaff->is_active ? 'active' : 'inactive'
                ]);
            }

            Log::info('Faculty staff status toggled', [
                'user_id' => $facultyStaff->id,
                'new_status' => $facultyStaff->is_active,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Faculty staff status updated successfully.',
                'is_active' => $facultyStaff->is_active,
                'status_text' => $facultyStaff->is_active ? 'Active' : 'Inactive',
                'status_class' => $facultyStaff->is_active ? 'success' : 'danger'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle faculty staff status', [
                'user_id' => $facultyStaff->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to update status.'], 500);
        }
    }

    /**
     * Generate a unique employee ID.
     */
    private function generateEmployeeId(): string
    {
        $year = date('Y');
        $lastEmployee = FacultyProfile::whereYear('created_at', $year)->count();
        return 'FAC' . $year . str_pad($lastEmployee + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a default password.
     */
    private function generateDefaultPassword(): string
    {
        return 'JBI' . date('Y') . rand(1000, 9999) . '!';
    }

    /**
     * Create a default faculty profile for a user.
     */
    private function createDefaultFacultyProfile(User $user): void
    {
        if (!$user || !$user->id) {
            throw new \Exception('Invalid user provided for faculty profile creation.');
        }

        $defaultDepartmentId = Department::where('is_active', true)->first()?->id;

        if (!$defaultDepartmentId) {
            throw new \Exception('No active departments available to create faculty profile.');
        }

        FacultyProfile::create([
            'user_id' => $user->id,
            'employee_id' => $this->generateEmployeeId(),
            'department_id' => $defaultDepartmentId,
            'designation' => 'Faculty Member',
            'position' => 'Faculty Member',
            'qualification' => 'Bachelor\'s Degree',
            'specialization' => 'General',
            'joining_date' => now(),
            'hire_date' => now(),
            'employment_type' => 'full_time',
            'employment_status' => 'pending',
            'application_status' => 'submitted',
            'years_of_experience' => 0,
            'status' => 'active',
            'qualifications' => [],
            'experience' => [],
        ]);
    }
}
