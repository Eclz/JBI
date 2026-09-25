<?php

namespace App\Http\Controllers;

use App\Models\HrEmployee;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumanResourcesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isStudent()) {
                abort(403, 'Students do not have access to the Human Resources module.');
            }
            return $next($request);
        });
    }
    public function index()
    {
        $user = Auth::user();
        $isManager = $user ? ($user->isHrStaff() || $user->isAdmin()) : false;

        if (!$isManager) {
            return view('human-resources.ess');
        }

        return view('human-resources.index', [
            'staffCount' => HrEmployee::where('status', 'Active')->count(),
            'leaveRequests' => \App\Models\LeaveRequest::where('status', 'pending')->count(),
            'onboardingCount' => HrEmployee::where('status', 'Onboarding')->count(),
            'recentStaff' => User::with('hrProfile')->whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->latest()->take(5)->get(),
            'isManager' => $isManager,
        ]);
    }

    public function staffIndex()
    {
        $employees = User::with('hrProfile', 'roleCatalog')
            ->whereNotIn('role', ['student', 'applicant', 'parent', ''])
            ->whereNotNull('role')
            ->latest()
            ->get();
        return view('human-resources.staff', compact('employees'));
    }

    public function show(User $employee)
    {
        $employee->load('hrProfile');
        if (!$employee->hrProfile) {
            $employee->hrProfile = new HrEmployee([
                'employee_number' => 'Not Assigned',
                'job_title' => $employee->role ?? 'Staff',
                'department' => 'Not Assigned',
                'status' => 'Active',
            ]);
        }
        return view('human-resources.show', compact('employee'));
    }

    public function leavesIndex()
    {
        return view('human-resources.leaves');
    }

    public function directory()
    {
        $employees = User::with('hrProfile')
            ->whereNotIn('role', ['student', 'applicant', 'parent', ''])
            ->whereNotNull('role')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('human-resources.directory', compact('employees'));
    }

    public function section(string $section)
    {
        $permissions = [
            'employee-directory' => 'hr_core',
            'profiles' => 'hr_core',
            'org-chart' => 'hr_core',
            'job-roles' => 'hr_core',
            'ess' => 'hr_core',
            'time-tracking' => 'hr_attendance',
            'shift-manager' => 'hr_attendance',
            'leave-management' => 'hr_attendance',
            'payroll' => 'hr_payroll',
            'expense-claims' => 'hr_payroll',
            'benefits' => 'hr_payroll',
            'recruiting' => 'hr_recruiting',
            'onboarding' => 'hr_recruiting',
            'offboarding' => 'hr_recruiting',
            'performance' => 'hr_talent',
            'learning' => 'hr_talent',
            'succession' => 'hr_talent',
        ];

        abort_unless(isset($permissions[$section]) && Auth::user()->hasPermission($permissions[$section], 'view'), 403);

        if ($section === 'payroll') {
            return redirect()->route('admin.finance.payroll.index');
        }

        if ($section === 'leave-management') {
            return redirect()->route('human-resources.leaves.index');
        }

        if ($section === 'profiles') {
            return redirect()->route('human-resources.staff.index');
        }

        if ($section === 'org-chart') {
            $employees = User::with(['hrProfile.manager'])->whereHas('hrProfile')->get();
            $departments = HrEmployee::select('department')->whereNotNull('department')->distinct()->pluck('department');
            $stats = [
                'total_staff' => HrEmployee::count(),
                'departments' => $departments->count(),
                'managers' => HrEmployee::whereNotNull('manager_id')->distinct('manager_id')->count('manager_id'), // unique managers
                'vacancies' => 0 // Placeholder until we build a recruitment module
            ];
            
            // Build the tree data
            $tree = [];
            $allEmployees = $employees->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'title' => $user->hrProfile->job_title ?? 'Staff',
                    'department' => $user->hrProfile->department ?? 'General',
                    'photo' => $user->profile_picture_url,
                    'manager_id' => $user->hrProfile->manager_id,
                    'status' => $user->hrProfile->status ?? 'Active',
                    'employee_id' => $user->hrProfile->employee_number,
                    'email' => $user->email,
                    'phone' => $user->phone
                ];
            })->keyBy('id')->toArray();

            return view("human-resources.org-chart", compact('allEmployees', 'departments', 'stats'));
        }

        if ($section === 'onboarding') {
            $stats = [
                'total' => \App\Models\HrOnboarding::count(),
                'not_started' => \App\Models\HrOnboarding::where('status', 'Not Started')->count(),
                'in_progress' => \App\Models\HrOnboarding::where('status', 'In Progress')->count(),
                'completed' => \App\Models\HrOnboarding::where('status', 'Completed')->count(),
                'overdue' => \App\Models\HrOnboarding::where('status', 'Overdue')->count(),
            ];
            $onboardings = \App\Models\HrOnboarding::with(['user.hrProfile', 'hrOfficer'])->latest()->get();
            $users = User::whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->orderBy('first_name')->get();

            return view("human-resources.onboarding", compact('stats', 'onboardings', 'users'));
        }

        if ($section === 'succession') {
            $stats = [
                'critical_positions' => \App\Models\HrSuccessionPlan::count(),
                'with_successors' => \App\Models\HrSuccessionPlan::has('successors')->count(),
                'without_successors' => \App\Models\HrSuccessionPlan::doesntHave('successors')->count(),
                'total_successors' => \App\Models\HrSuccessor::count(),
            ];
            $plans = \App\Models\HrSuccessionPlan::with(['currentHolder', 'successors.user'])->latest()->get();
            $users = User::whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->orderBy('first_name')->get();
            
            return view("human-resources.succession", compact('stats', 'plans', 'users'));
        }

        if ($section === 'recruiting') {
            $stats = [
                'open_vacancies' => \App\Models\HrVacancy::where('status', 'Open')->count(),
                'total_applications' => \App\Models\HrApplicant::count(),
                'shortlisted' => \App\Models\HrApplicant::where('status', 'Shortlisted')->count(),
                'hired' => \App\Models\HrApplicant::where('status', 'Hired')->count(),
            ];
            $vacancies = \App\Models\HrVacancy::withCount(['applicants' => function($q) {
                $q->whereNotIn('status', ['Rejected']);
            }])->with('hiringManager')->latest()->get();

            return view("human-resources.recruiting", compact('stats', 'vacancies'));
        }

        if ($section === 'shift-manager') {
            $shifts = \App\Models\HrShift::withCount('assignments')->get();
            $assignments = \App\Models\HrShiftAssignment::with(['user.hrProfile', 'shift'])->latest()->get();
            $users = User::whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->orderBy('first_name')->get();
            
            $stats = [
                'total_shifts' => $shifts->count(),
                'total_assignments' => $assignments->count(),
                'active_assignments' => \App\Models\HrShiftAssignment::where(function($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
                })->count(),
            ];

            return view("human-resources.shift-manager", compact('shifts', 'assignments', 'users', 'stats'));
        }

        if ($section === 'expense-claims') {
            $claims = \App\Models\HrExpenseClaim::with(['user.hrProfile', 'approver'])->latest()->get();
            $stats = [
                'pending' => $claims->where('status', 'Pending')->count(),
                'approved' => $claims->where('status', 'Approved')->count(),
                'paid' => $claims->where('status', 'Paid')->count(),
                'rejected' => $claims->where('status', 'Rejected')->count(),
                'total_pending_amount' => $claims->where('status', 'Pending')->sum('amount'),
            ];
            $users = User::whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->orderBy('first_name')->get();

            return view("human-resources.expense-claims", compact('claims', 'stats', 'users'));
        }

        if ($section === 'benefits') {
            $plans = \App\Models\HrBenefitPlan::withCount('enrollments')->get();
            $enrollments = \App\Models\HrBenefitEnrollment::with(['user.hrProfile', 'plan'])->latest()->get();
            $users = User::whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->orderBy('first_name')->get();
            
            $stats = [
                'total_plans' => $plans->count(),
                'active_enrollments' => $enrollments->where('status', 'Active')->count(),
                'monthly_company_cost' => $plans->sum('company_cost') * $enrollments->where('status', 'Active')->count(), // basic estimate
            ];

            return view("human-resources.benefits", compact('plans', 'enrollments', 'users', 'stats'));
        }

        if ($section === 'performance') {
            $reviews = \App\Models\HrPerformanceReview::with(['user.hrProfile', 'reviewer'])->latest()->get();
            $goals = \App\Models\HrPerformanceGoal::with('user.hrProfile')->latest()->get();
            $users = User::whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->orderBy('first_name')->get();
            
            $stats = [
                'active_goals' => $goals->whereNotIn('status', ['Completed', 'Cancelled'])->count(),
                'completed_goals' => $goals->where('status', 'Completed')->count(),
                'pending_reviews' => $reviews->where('status', 'Draft')->count(),
                'avg_rating' => round($reviews->where('status', 'Completed')->avg('overall_rating') ?? 0, 1),
            ];

            return view("human-resources.performance", compact('reviews', 'goals', 'users', 'stats'));
        }

        if ($section === 'learning') {
            $courses = \App\Models\HrTrainingCourse::withCount(['enrollments' => function($q) {
                $q->whereNotIn('status', ['Failed', 'Cancelled']);
            }])->where('status', 'Active')->get();
            
            $enrollments = \App\Models\HrTrainingEnrollment::with(['user.hrProfile', 'course'])->latest()->get();
            $users = User::whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->orderBy('first_name')->get();

            $stats = [
                'active_courses' => $courses->count(),
                'active_learners' => $enrollments->whereIn('status', ['Enrolled', 'In Progress'])->count(),
                'completed_trainings' => $enrollments->where('status', 'Completed')->count(),
                'total_hours' => $enrollments->where('status', 'Completed')->sum(function($enr) {
                    return $enr->course->duration_hours ?? 0;
                }),
            ];

            return view("human-resources.learning", compact('courses', 'enrollments', 'users', 'stats'));
        }

        if (view()->exists("human-resources.{$section}")) {
            return view("human-resources.{$section}");
        }

        return view('human-resources.section', [
            'section' => ucwords(str_replace('-', ' ', $section)),
        ]);
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('first_name')->get();
        return view('human-resources.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_number' => 'required|string|max:50|unique:hr_employees,employee_number',
            'job_title' => 'required|string|max:150',
            'department' => 'nullable|string|max:150',
            'employment_type' => 'required|string|max:50',
            'salary_band' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:255',
            'status' => 'required|in:Active,On Leave,Inactive',
            'notes' => 'nullable|string|max:2000',
        ]);
        $employee = HrEmployee::create($data);
        $this->notify($employee, 'Employee record created', "HR created employee record {$employee->employee_number}.", 'human-resources.staff.index');

        return redirect()->route('human-resources.staff.index')->with('success', 'Employee record created successfully.');
    }

    public function updateManager(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'manager_id' => 'nullable|exists:users,id'
        ]);

        if ($request->employee_id == $request->manager_id) {
            return back()->with('error', 'An employee cannot be their own manager.');
        }

        $employee = User::findOrFail($request->employee_id);
        
        if ($employee->hrProfile) {
            $employee->hrProfile->update(['manager_id' => $request->manager_id]);
        } else {
            return back()->with('error', 'Employee HR profile not found.');
        }

        return back()->with('success', 'Reporting manager updated successfully.');
    }

    public function edit(User $employee)
    {
        $hrProfile = $employee->hrProfile;
        if (!$hrProfile) {
            $hrProfile = new HrEmployee([
                'employee_number' => 'EMP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'job_title' => $employee->role,
                'status' => 'Active',
            ]);
        } elseif (empty($hrProfile->employee_number)) {
            $hrProfile->employee_number = 'EMP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        
        $jobRoles = \App\Models\HrJobRole::where('is_active', true)->orderBy('title')->get();
        return view('human-resources.edit', compact('employee', 'hrProfile', 'jobRoles'));
    }

    public function update(Request $request, User $employee)
    {
        $data = $request->validate([
            'employee_number' => 'required|string|max:50',
            'job_title' => 'required|string|max:150',
            'department' => 'nullable|string|max:150',
            'employment_type' => 'required|string|max:50',
            'salary_band' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:255',
            'status' => 'required|in:Active,On Leave,Inactive',
            'notes' => 'nullable|string|max:2000',
        ]);
        
        $data['user_id'] = $employee->id;
        
        if ($employee->hrProfile) {
            $employee->hrProfile->update($data);
        } else {
            HrEmployee::create($data);
        }
        
        $this->notifyUser($employee->id, 'Employee record updated', "HR updated your employee record.", 'human-resources.staff.index');

        return redirect()->route('human-resources.staff.index')->with('success', 'Employee record updated successfully.');
    }

    public function destroy(User $employee)
    {
        if ($employee->hrProfile) {
            $number = $employee->hrProfile->employee_number;
            $employee->hrProfile->delete();
            $employee->update(['is_active' => false]);
            $this->notifyUser($employee->id, 'Employee record removed', "Your HR employee record {$number} was removed and your account deactivated.", 'human-resources.index');
        } else {
            $employee->update(['is_active' => false]);
        }

        return redirect()->route('human-resources.staff.index')->with('success', 'Employee HR profile deleted and account deactivated successfully.');
    }

    private function notify(HrEmployee $employee, string $title, string $message, string $route): void
    {
        $this->notifyUser($employee->user_id ?: Auth::id(), $title, $message, $route);
    }

    private function notifyUser(int $userId, string $title, string $message, string $route): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'human_resources',
            'title' => $title,
            'message' => $message,
            'action_url' => route($route),
            'priority' => 'normal',
        ]);
    }

}
