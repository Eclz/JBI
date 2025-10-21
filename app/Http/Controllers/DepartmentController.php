<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Models\FacultyProfile;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        $query = Department::with(['faculty', 'headOfDepartment', 'facultyMembers.user', 'students.user', 'courses']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('faculty', function ($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Faculty filter
        if ($request->filled('faculty')) {
            $query->where('faculty_id', $request->faculty);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');

        if ($sortBy === 'faculty') {
            $query->join('faculties', 'departments.faculty_id', '=', 'faculties.id')
                  ->orderBy('faculties.name', $sortOrder)
                  ->select('departments.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $departments = $query->paginate(20)->withQueryString();

        // Get faculties for filter dropdown
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();

        // Calculate statistics
        $stats = [
            'total' => Department::count(),
            'active' => Department::where('is_active', true)->count(),
            'inactive' => Department::where('is_active', false)->count(),
            'with_head' => Department::whereNotNull('head_of_department_id')->count(),
        ];

        return view('admin.departments.index', compact('departments', 'faculties', 'stats'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $potentialHeads = User::where('role', 'faculty')
                             ->where('is_active', true)
                             ->with('facultyProfile')
                             ->orderBy('first_name')
                             ->orderBy('last_name')
                             ->get();

        if ($faculties->isEmpty()) {
            return redirect()->route('admin.departments.index')
                           ->with('error', 'No active faculties available. Please create a faculty first.');
        }

        return view('admin.departments.create', compact('faculties', 'potentialHeads'));
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request)
    {
        DB::beginTransaction();

        try {
            $department = Department::create($request->validated());

            DB::commit();

            Log::info('Department created successfully', [
                'department_id' => $department->id,
                'name' => $department->name,
                'created_by' => auth()->id()
            ]);

            return redirect()->route('admin.departments.index')
                           ->with('success', 'Department created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create department', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);

            return back()->withErrors(['error' => 'Failed to create department: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        $department->load([
            'faculty',
            'headOfDepartment.facultyProfile',
            'facultyMembers.user',
            'students.user',
            'courses.semester',
            'courses.enrollments'
        ]);

        // Get department statistics
        $stats = [
            'faculty_count' => $department->facultyMembers->count(),
            'student_count' => $department->students->count(),
            'course_count' => $department->courses->count(),
            'active_courses' => $department->courses->where('is_active', true)->count(),
        ];

        // Get available faculty members for head assignment
        $availableFacultyMembers = User::where('role', 'faculty')
                                      ->where('is_active', true)
                                      ->whereHas('facultyProfile', function ($query) use ($department) {
                                          $query->where('department_id', $department->id);
                                      })
                                      ->with('facultyProfile')
                                      ->orderBy('first_name')
                                      ->orderBy('last_name')
                                      ->get();

        return view('admin.departments.show', compact('department', 'stats', 'availableFacultyMembers'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $potentialHeads = User::where('role', 'faculty')
                             ->where('is_active', true)
                             ->with('facultyProfile')
                             ->orderBy('first_name')
                             ->orderBy('last_name')
                             ->get();

        return view('admin.departments.edit', compact('department', 'faculties', 'potentialHeads'));
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        DB::beginTransaction();

        try {
            $department->update($request->validated());

            DB::commit();

            Log::info('Department updated successfully', [
                'department_id' => $department->id,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('admin.departments.index')
                           ->with('success', 'Department updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update department', [
                'department_id' => $department->id,
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);

            return back()->withErrors(['error' => 'Failed to update department: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department)
    {
        // Check if department has faculty members
        if ($department->facultyMembers()->count() > 0) {
            return back()->with('error', 'Cannot delete department with faculty members. Please reassign faculty first.');
        }

        // Check if department has students
        if ($department->students()->count() > 0) {
            return back()->with('error', 'Cannot delete department with students. Please reassign students first.');
        }

        // Check if department has courses
        if ($department->courses()->count() > 0) {
            return back()->with('error', 'Cannot delete department with courses. Please reassign courses first.');
        }

        DB::beginTransaction();

        try {
            $departmentName = $department->name;
            $department->delete();

            DB::commit();

            Log::info('Department deleted successfully', [
                'department_name' => $departmentName,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('admin.departments.index')
                           ->with('success', 'Department deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete department', [
                'department_id' => $department->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete department: ' . $e->getMessage());
        }
    }

    /**
     * Toggle department status via AJAX.
     */
    public function toggleStatus(Department $department)
    {
        try {
            $department->update(['is_active' => !$department->is_active]);

            Log::info('Department status toggled', [
                'department_id' => $department->id,
                'new_status' => $department->is_active,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department status updated successfully.',
                'is_active' => $department->is_active,
                'status_text' => $department->is_active ? 'Active' : 'Inactive',
                'status_class' => $department->is_active ? 'success' : 'danger'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle department status', [
                'department_id' => $department->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to update status.'], 500);
        }
    }

    /**
     * Assign a faculty member as head of department.
     */
    public function assignHead(Request $request, Department $department)
    {
        $request->validate([
            'head_of_department_id' => 'required|exists:users,id',
        ]);

        // Verify the user is a faculty member in this department
        $facultyMember = User::where('id', $request->head_of_department_id)
                            ->where('role', 'faculty')
                            ->where('is_active', true)
                            ->whereHas('facultyProfile', function ($query) use ($department) {
                                $query->where('department_id', $department->id);
                            })
                            ->first();

        if (!$facultyMember) {
            return back()->with('error', 'Selected faculty member is not assigned to this department.');
        }

        DB::beginTransaction();

        try {
            $department->update(['head_of_department_id' => $request->head_of_department_id]);

            DB::commit();

            Log::info('Department head assigned', [
                'department_id' => $department->id,
                'head_id' => $request->head_of_department_id,
                'assigned_by' => auth()->id()
            ]);

            return back()->with('success', 'Department head assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to assign department head', [
                'department_id' => $department->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to assign department head: ' . $e->getMessage());
        }
    }

    /**
     * Show staff assignment guide.
     */
    public function staffAssignmentGuide()
    {
        $faculties = Faculty::with(['departments.facultyMembers.user', 'departments.headOfDepartment'])
                           ->where('is_active', true)
                           ->get();

        $departments = Department::where('is_active', true)->count();
        $facultyStaff = User::where('role', 'faculty')->where('is_active', true)->count();

        return view('admin.faculty-staff.assign-to-department', compact('faculties', 'departments', 'facultyStaff'));
    }
}
