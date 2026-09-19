<?php

namespace App\Http\Controllers;

use App\Models\HrEmployee;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumanResourcesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('human-resources.index', [
            'staffCount' => HrEmployee::where('status', 'Active')->count(),
            'leaveRequests' => 0,
            'isManager' => $user ? ($user->isHrStaff() || $user->isAdmin()) : false,
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
        return view('human-resources.show', compact('employee'));
    }

    public function leavesIndex()
    {
        return view('human-resources.leaves');
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

    public function edit(User $employee)
    {
        $hrProfile = $employee->hrProfile;
        return view('human-resources.edit', compact('employee', 'hrProfile'));
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
            $this->notifyUser($employee->id, 'Employee record removed', "Your HR employee record {$number} was removed.", 'human-resources.index');
        }

        return redirect()->route('human-resources.staff.index')->with('success', 'Employee HR profile deleted successfully.');
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
