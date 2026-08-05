<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgrammeCoursesController extends Controller
{
    public function myProgramme()
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;

        $program = null;
        if ($studentProfile?->program_id) {
            $program = Program::with('department')->find($studentProfile->program_id);
        }

        // Get courses for program or general active courses
        $query = Course::query();
        if ($program) {
            $query->where(function($q) use ($program) {
                $q->where('program_id', $program->id)
                  ->orWhere('department_id', $program->department_id)
                  ->orWhereNull('program_id');
            });
        }

        $allCourses = $query->get();

        // Sort in collection safely
        $sortedCourses = $allCourses->sortBy([
            fn($a, $b) => ($a->year_of_study ?? 1) <=> ($b->year_of_study ?? 1),
            fn($a, $b) => ($a->semester_id ?? 1) <=> ($b->semester_id ?? 1),
            fn($a, $b) => strcmp($a->code ?? '', $b->code ?? ''),
        ]);

        // Group courses by Year and Semester
        $groupedCourses = [];
        foreach ($sortedCourses as $course) {
            $yr = $course->year_of_study ?? 1;
            $sem = $course->semester_id ?? 1;
            $groupedCourses[$yr][$sem][] = $course;
        }

        // Student's enrolled course IDs
        $enrolledCourseIds = CourseEnrollment::where('user_id', $user->id)
            ->pluck('status', 'course_id')
            ->toArray();

        return view('student.my_programme.index', compact('user', 'studentProfile', 'program', 'groupedCourses', 'enrolledCourseIds'));
    }

    public function showEnrollment()
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;
        $academicYears = AcademicYear::where('is_active', true)->orWhere('is_current', true)->get();
        if ($academicYears->isEmpty()) {
            $academicYears = AcademicYear::all();
        }
        $semesters = Semester::all();

        return view('student.enrollment.index', compact('user', 'studentProfile', 'academicYears', 'semesters'));
    }

    public function processEnrollment(Request $request)
    {
        $request->validate([
            'year_of_study' => 'required|integer|min:1|max:6',
            'current_semester' => 'required|integer|in:1,2',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $user = Auth::user();
        $studentProfile = $user->studentProfile;

        if ($studentProfile) {
            $studentProfile->update([
                'year_of_study' => $request->year_of_study,
                'current_semester' => $request->current_semester,
                'registration_fee_paid_at' => now(),
            ]);
        }

        return redirect()->route('student.my-programme')->with('success', 'Successfully enrolled for Year ' . $request->year_of_study . ' Semester ' . $request->current_semester . '!');
    }
}
