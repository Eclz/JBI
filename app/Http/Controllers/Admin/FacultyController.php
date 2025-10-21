<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FacultyController extends Controller
{
    /**
     * Display a listing of faculties.
     */
    public function index(Request $request)
    {
        $query = Faculty::with(['dean', 'departments']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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

        // Sorting
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $faculties = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Faculty::count(),
            'active' => Faculty::where('is_active', true)->count(),
            'inactive' => Faculty::where('is_active', false)->count(),
            'with_dean' => Faculty::whereNotNull('dean_id')->count(),
            'total_departments' => Department::count(),
        ];

        return view('admin.faculties.index', compact('faculties', 'stats'));
    }

    /**
     * Show the form for creating a new faculty.
     */
    public function create()
    {
        // Get potential deans (faculty members with appropriate qualifications)
        $potentialDeans = User::where('role', 'faculty')
            ->where('is_active', true)
            ->whereHas('facultyProfile', function ($query) {
                $query->where('employment_status', 'active');
            })
            ->orderBy('first_name')
            ->get();

        return view('admin.faculties.create', compact('potentialDeans'));
    }

    /**
     * Store a newly created faculty.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:faculties,code',
            'description' => 'nullable|string|max:1000',
            'dean_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:faculties,email',
            'website' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Faculty name is required.',
            'code.required' => 'Faculty code is required.',
            'code.unique' => 'This faculty code is already taken.',
            'email.unique' => 'This email address is already in use.',
            'dean_id.exists' => 'The selected dean is invalid.',
        ]);

        try {
            DB::beginTransaction();

            // Create the faculty
            $faculty = Faculty::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'dean_id' => $validated['dean_id'] ?? null,
                'location' => $validated['location'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'website' => $validated['website'] ?? null,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            Log::info('Faculty created successfully', [
                'faculty_id' => $faculty->id,
                'faculty_name' => $faculty->name,
                'created_by' => auth()->id()
            ]);

            return redirect()->route('admin.faculties.index')
                           ->with('success', 'Faculty "' . $faculty->name . '" created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create faculty', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()
                ->withErrors(['error' => 'Failed to create faculty: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified faculty.
     */
    public function show(Faculty $faculty)
    {
        $faculty->load([
            'dean.facultyProfile',
            'departments' => function ($query) {
                $query->withCount(['facultyMembers', 'students', 'courses']);
            }
        ]);

        // Get faculty statistics
        $stats = [
            'departments_count' => $faculty->departments()->count(),
            'active_departments_count' => $faculty->activeDepartments()->count(),
            'faculty_members_count' => $faculty->departments->sum('faculty_members_count'),
            'students_count' => $faculty->departments->sum('students_count'),
            'courses_count' => $faculty->departments->sum('courses_count'),
        ];

        return view('admin.faculties.show', compact('faculty', 'stats'));
    }

    /**
     * Show the form for editing the specified faculty.
     */
    public function edit(Faculty $faculty)
    {
        // Get potential deans
        $potentialDeans = User::where('role', 'faculty')
            ->where('is_active', true)
            ->whereHas('facultyProfile', function ($query) {
                $query->where('employment_status', 'active');
            })
            ->orderBy('first_name')
            ->get();

        return view('admin.faculties.edit', compact('faculty', 'potentialDeans'));
    }

    /**
     * Update the specified faculty.
     */
    public function update(Request $request, Faculty $faculty)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('faculties', 'code')->ignore($faculty->id)
            ],
            'description' => 'nullable|string|max:1000',
            'dean_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('faculties', 'email')->ignore($faculty->id)
            ],
            'website' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $faculty->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'dean_id' => $validated['dean_id'] ?? null,
                'location' => $validated['location'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'website' => $validated['website'] ?? null,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            Log::info('Faculty updated successfully', [
                'faculty_id' => $faculty->id,
                'faculty_name' => $faculty->name,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('admin.faculties.index')
                           ->with('success', 'Faculty "' . $faculty->name . '" updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update faculty', [
                'faculty_id' => $faculty->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()
                ->withErrors(['error' => 'Failed to update faculty: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified faculty.
     */
    public function destroy(Faculty $faculty)
    {
        // Check if faculty has departments
        if ($faculty->departments()->count() > 0) {
            return back()->with('error', 'Cannot delete faculty with existing departments. Please reassign or delete departments first.');
        }

        try {
            DB::beginTransaction();

            $facultyName = $faculty->name;
            $faculty->delete();

            DB::commit();

            Log::info('Faculty deleted successfully', [
                'faculty_name' => $facultyName,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('admin.faculties.index')
                           ->with('success', 'Faculty "' . $facultyName . '" deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete faculty', [
                'faculty_id' => $faculty->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to delete faculty: ' . $e->getMessage());
        }
    }

    /**
     * Toggle faculty status.
     */
    public function toggleStatus(Faculty $faculty)
    {
        try {
            DB::beginTransaction();

            $newStatus = !$faculty->is_active;
            $faculty->update(['is_active' => $newStatus]);

            DB::commit();

            Log::info('Faculty status toggled', [
                'faculty_id' => $faculty->id,
                'faculty_name' => $faculty->name,
                'new_status' => $newStatus,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Faculty status updated successfully.',
                'is_active' => $newStatus,
                'status_text' => $newStatus ? 'Active' : 'Inactive',
                'status_class' => $newStatus ? 'success' : 'danger'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to toggle faculty status', [
                'faculty_id' => $faculty->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
