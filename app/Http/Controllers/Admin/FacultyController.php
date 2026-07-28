<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $query = Faculty::with(['dean', 'departments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $faculties = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Faculty::count(),
            'active' => Faculty::where('is_active', true)->count(),
            'inactive' => Faculty::where('is_active', false)->count(),
            'with_dean' => Faculty::whereNotNull('dean_id')->count(),
            'total_departments' => \App\Models\Department::count(),
        ];

        return view('admin.faculties.index', compact('faculties', 'stats'));
    }

    public function create()
    {
        $potentialDeans = User::where('role', 'faculty')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.faculties.create', compact('potentialDeans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:faculties,code',
            'description' => 'nullable|string|max:2000',
            'dean_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $faculty = Faculty::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'dean_id' => $validated['dean_id'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'location' => $validated['location'] ?? null,
                'website' => $validated['website'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('admin.faculties.show', $faculty)
                ->with('success', 'Faculty created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create faculty', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['_token'])
            ]);

            return back()->withErrors(['error' => 'Failed to create faculty.'])->withInput();
        }
    }

    public function show(Faculty $faculty)
    {
        $faculty->load(['dean', 'departments.head', 'departments.courses', 'departments.facultyMembers', 'departments.students']);

        $stats = [
            'departments_count' => $faculty->departments->count(),
            'faculty_members_count' => $faculty->facultyMembers()->count(),
            'students_count' => $faculty->students()->count(),
            'courses_count' => $faculty->courses()->count(),
        ];

        return view('admin.faculties.show', compact('faculty', 'stats'));
    }

    public function edit(Faculty $faculty)
    {
        $potentialDeans = User::where('role', 'faculty')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.faculties.edit', compact('faculty', 'potentialDeans'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:faculties,code,' . $faculty->id,
            'description' => 'nullable|string|max:2000',
            'dean_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $faculty->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'dean_id' => $validated['dean_id'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'location' => $validated['location'] ?? null,
                'website' => $validated['website'] ?? null,
                'is_active' => $request->boolean('is_active', $faculty->is_active),
            ]);

            DB::commit();

            return redirect()->route('admin.faculties.show', $faculty)
                ->with('success', 'Faculty updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update faculty', [
                'faculty_id' => $faculty->id,
                'error' => $e->getMessage(),
                'request_data' => $request->except(['_token'])
            ]);

            return back()->withErrors(['error' => 'Failed to update faculty.'])->withInput();
        }
    }

    public function destroy(Faculty $faculty)
    {
        if ($faculty->departments()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete faculty with departments.']);
        }

        $faculty->delete();

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty deleted successfully.');
    }

    public function toggleStatus(Faculty $faculty)
    {
        $faculty->update(['is_active' => !$faculty->is_active]);

        return back()->with('success', 'Faculty status updated successfully.');
    }
}
