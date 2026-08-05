<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Exam;
use App\Models\Timetable;
use App\Models\Assignment;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::where('is_current', true)->first() ?? AcademicYear::first();
        $semesters = Semester::all();

        // Upcoming exams and assessments
        $upcomingExams = Exam::with('course')
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(10)
            ->get();

        $upcomingAssignments = Assignment::with('course')
            ->where('due_date', '>=', now())
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();

        $timetables = Timetable::with(['course', 'faculty'])->get();

        return view('calendar.index', compact('currentYear', 'semesters', 'upcomingExams', 'upcomingAssignments', 'timetables'));
    }
}
