<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Department;
use App\Models\ProgramLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with(['department', 'level']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('level')) {
            $query->where('program_level_id', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $programs = $query->orderBy('name')->paginate(20)->withQueryString();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $levels = ProgramLevel::where('is_active', true)->orderBy('name')->get();

        return view('admin.programs.index', compact('programs', 'departments', 'levels'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $levels = ProgramLevel::where('is_active', true)->orderBy('name')->get();

        return view('admin.programs.create', compact('departments', 'levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'program_level_id' => 'nullable|exists:program_levels,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code',
            'description' => 'nullable|string|max:2000',
            // 'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            Program::create([
                'department_id' => $validated['department_id'],
                'program_level_id' => $validated['program_level_id'] ?? null,
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('admin.programs.index')
                ->with('success', 'Program created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create program', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create program.'])->withInput();
        }
    }

    public function show(Program $program)
    {
        $program->load(['department', 'level', 'courses']);

        return view('admin.programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $levels = ProgramLevel::where('is_active', true)->orderBy('name')->get();

        return view('admin.programs.edit', compact('program', 'departments', 'levels'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'program_level_id' => 'nullable|exists:program_levels,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code,' . $program->id,
            'description' => 'nullable|string|max:2000',
            // 'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $program->update([
                'department_id' => $validated['department_id'],
                'program_level_id' => $validated['program_level_id'] ?? null,
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active', $program->is_active),
            ]);

            DB::commit();

            return redirect()->route('admin.programs.show', $program)
                ->with('success', 'Program updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update program', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update program.'])->withInput();
        }
    }

    public function destroy(Program $program)
    {
        if ($program->courses()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete a program with courses.']);
        }

        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program deleted successfully.');
    }
}
