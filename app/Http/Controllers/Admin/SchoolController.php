<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Display a listing of schools.
     */
    public function index(Request $request)
    {
        $schools = School::with(['dean', 'departments'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->withCount('departments')
            ->latest()
            ->paginate(10);

        return view('admin.schools.index', compact('schools'));
    }

    /**
     * Show form to create a new school.
     */
    public function create()
    {
        $deans = User::whereIn('role', ['admin', 'faculty', 'bursar'])->get();
        return view('admin.schools.create', compact('deans'));
    }

    /**
     * Store a newly created school in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:schools,code',
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        School::create($validated);

        return redirect()->route('admin.schools.index')
            ->with('success', 'Academic School created successfully.');
    }

    /**
     * Display the specified school.
     */
    public function show(School $school)
    {
        $school->load(['dean', 'departments.headOfDepartment']);
        return view('admin.schools.show', compact('school'));
    }

    /**
     * Show form to edit the specified school.
     */
    public function edit(School $school)
    {
        $deans = User::whereIn('role', ['admin', 'faculty', 'bursar'])->get();
        return view('admin.schools.edit', compact('school', 'deans'));
    }

    /**
     * Update the specified school.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:schools,code,' . $school->id,
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $school->update($validated);

        return redirect()->route('admin.schools.index')
            ->with('success', 'Academic School updated successfully.');
    }

    /**
     * Remove or deactivate the specified school.
     */
    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('admin.schools.index')
            ->with('success', 'Academic School deleted successfully.');
    }
}
