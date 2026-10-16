<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use App\Models\FacilityRoom;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $query = Timetable::with(['course', 'faculty', 'program', 'academicYear', 'semester']);

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('year_of_study')) {
            $query->where('year_of_study', $request->year_of_study);
        }

        $timetables = (clone $query)->orderBy('day_of_week')->orderBy('start_time')->paginate(20);
        $allSlots = $query->orderBy('day_of_week')->orderBy('start_time')->get();
        $programs = Program::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $semesters = Semester::all();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $timeSlots = [
            '06:00 - 06:30', '06:30 - 07:00', '07:00 - 07:30', '07:30 - 08:00',
            '08:00 - 08:30', '08:30 - 09:00', '09:00 - 09:30', '09:30 - 10:00',
            '10:00 - 10:30', '10:30 - 11:00', '11:00 - 11:30', '11:30 - 12:00',
            '12:00 - 12:30', '12:30 - 13:00', '13:00 - 13:30', '13:30 - 14:00',
            '14:00 - 14:30', '14:30 - 15:00', '15:00 - 15:30', '15:30 - 16:00',
            '16:00 - 17:00',
        ];

        return view('admin.timetables.index', compact('timetables', 'allSlots', 'programs', 'academicYears', 'semesters', 'days', 'timeSlots'));
    }

    public function create()
    {
        $courses = Course::orderBy('code')->get();
        $programs = Program::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $semesters = Semester::all();
        $facultyMembers = User::where('role', 'faculty')->orderBy('first_name')->get();
        $rooms = FacilityRoom::orderBy('name')->get();

        return view('admin.timetables.create', compact('courses', 'programs', 'academicYears', 'semesters', 'facultyMembers', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'faculty_id' => 'nullable|exists:users,id',
            'program_id' => 'nullable|exists:programs,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'year_of_study' => 'required|integer|min:1|max:6',
            'semester_number' => 'required|integer|in:1,2',
            'type' => 'required|in:teaching,tests,exams',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'room_venue' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Timetable::create($validated);

        return redirect()->route('admin.timetables.index')->with('success', 'Timetable slot created successfully!');
    }

    public function edit(Timetable $timetable)
    {
        $courses = Course::orderBy('code')->get();
        $programs = Program::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $semesters = Semester::all();
        $facultyMembers = User::where('role', 'faculty')->orderBy('first_name')->get();
        $rooms = FacilityRoom::orderBy('name')->get();

        return view('admin.timetables.edit', compact('timetable', 'courses', 'programs', 'academicYears', 'semesters', 'facultyMembers', 'rooms'));
    }

    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'faculty_id' => 'nullable|exists:users,id',
            'program_id' => 'nullable|exists:programs,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'year_of_study' => 'required|integer|min:1|max:6',
            'semester_number' => 'required|integer|in:1,2',
            'type' => 'required|in:teaching,tests,exams',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'room_venue' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $timetable->update($validated);

        return redirect()->route('admin.timetables.index')->with('success', 'Timetable slot updated successfully!');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return redirect()->route('admin.timetables.index')->with('success', 'Timetable slot deleted successfully!');
    }
}
