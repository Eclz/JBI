<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->paginate(20);

        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:9',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            // 'is_current' => 'nullable|boolean',
            // 'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            if ($request->boolean('is_current')) {
                AcademicYear::query()->update(['is_current' => false]);
            }

            AcademicYear::create([
                'name' => $validated['name'],
                'year' => $validated['year'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_current' => $request->boolean('is_current', false),
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('admin.academic-years.index')
                ->with('success', 'Academic year created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create academic year', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create academic year.'])->withInput();
        }
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:9',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            // 'is_current' => 'nullable|boolean',
            // 'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            if ($request->boolean('is_current')) {
                AcademicYear::query()->update(['is_current' => false]);
            }

            $academicYear->update([
                'name' => $validated['name'],
                'year' => $validated['year'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_current' => $request->boolean('is_current', $academicYear->is_current),
                'is_active' => $request->boolean('is_active', $academicYear->is_active),
            ]);

            DB::commit();

            return redirect()->route('admin.academic-years.index')
                ->with('success', 'Academic year updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update academic year', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update academic year.'])->withInput();
        }
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->semesters()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete an academic year with semesters.']);
        }

        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }
}
