<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    /**
     * Display a listing of faculties.
     */
    public function index()
    {
        $faculties = Faculty::with(['dean'])
            ->withCount('departments')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new faculty.
     */
    public function create()
    {
        $deans = User::whereIn('role', ['admin', 'faculty'])->orderBy('first_name')->get();
        return view('admin.faculties.create', compact('deans'));
    }

    /**
     * Store a newly created faculty in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:faculties,code',
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Faculty::create($validated);

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty created successfully.');
    }

    /**
     * Display the specified faculty.
     */
    public function show(Faculty $faculty)
    {
        $faculty->load(['dean', 'departments.headOfDepartment']);
        return view('admin.faculties.show', compact('faculty'));
    }

    /**
     * Show the form for editing the specified faculty.
     */
    public function edit(Faculty $faculty)
    {
        $deans = User::whereIn('role', ['admin', 'faculty'])->orderBy('first_name')->get();
        return view('admin.faculties.edit', compact('faculty', 'deans'));
    }

    /**
     * Update the specified faculty in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:faculties,code,' . $faculty->id,
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $faculty->update($validated);

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty updated successfully.');
    }

    /**
     * Remove the specified faculty from storage.
     */
    public function destroy(Faculty $faculty)
    {
        if ($faculty->departments()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete faculty. It has linked departments under it.']);
        }

        $faculty->delete();

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty deleted successfully.');
    }
}
