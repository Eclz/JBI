<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->paginate(20);

        return view('admin.semesters.index', compact('semesters'));
    }

    public function create()
    {
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();

        return view('admin.semesters.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start' => 'required|date|before_or_equal:registration_end',
            'registration_end' => 'required|date|after_or_equal:registration_start',
            // 'is_current' => 'nullable|boolean',
            // 'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            if ($request->boolean('is_current')) {
                Semester::query()->update(['is_current' => false]);
                AcademicYear::query()->update(['is_current' => false]);
                AcademicYear::where('id', $validated['academic_year_id'])->update(['is_current' => true]);
            }

            Semester::create([
                'academic_year_id' => $validated['academic_year_id'],
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'registration_start' => $validated['registration_start'],
                'registration_end' => $validated['registration_end'],
                'is_current' => $request->boolean('is_current', false),
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('admin.semesters.index')
                ->with('success', 'Semester created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create semester', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create semester.'])->withInput();
        }
    }

    public function edit(Semester $semester)
    {
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();

        return view('admin.semesters.edit', compact('semester', 'academicYears'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start' => 'required|date|before_or_equal:registration_end',
            'registration_end' => 'required|date|after_or_equal:registration_start',
            // 'is_current' => 'nullable|boolean',
            // 'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            if ($request->boolean('is_current')) {
                Semester::query()->update(['is_current' => false]);
                AcademicYear::query()->update(['is_current' => false]);
                AcademicYear::where('id', $validated['academic_year_id'])->update(['is_current' => true]);
            }

            $semester->update([
                'academic_year_id' => $validated['academic_year_id'],
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'registration_start' => $validated['registration_start'],
                'registration_end' => $validated['registration_end'],
                'is_current' => $request->boolean('is_current', $semester->is_current),
                'is_active' => $request->boolean('is_active', $semester->is_active),
            ]);

            DB::commit();

            return redirect()->route('admin.semesters.index')
                ->with('success', 'Semester updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update semester', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update semester.'])->withInput();
        }
    }

    public function destroy(Semester $semester)
    {
        if ($semester->courses()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete a semester with courses.']);
        }

        $semester->delete();

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester deleted successfully.');
    }
}
