<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $faculty = Auth::user();
        $scope = $request->input('scope', 'my'); // 'my' or 'all'
        $type = $request->input('type', 'teaching'); // 'teaching', 'tests', 'exams'
        $programId = $request->input('program_id');
        $yearOfStudy = $request->input('year_of_study');
        $dayOfWeek = $request->input('day_of_week');

        $query = Timetable::with(['course.instructor', 'faculty', 'program', 'academicYear', 'semester'])
            ->where('type', $type);

        if ($scope === 'my') {
            $query->where(function($q) use ($faculty) {
                $q->where('faculty_id', $faculty->id)
                  ->orWhereHas('course', function($cq) use ($faculty) {
                      $cq->where('instructor_id', $faculty->id);
                  });
            });
        }

        if ($programId) {
            $query->where('program_id', $programId);
        }

        if ($yearOfStudy) {
            $query->where('year_of_study', $yearOfStudy);
        }

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        $slots = $query->orderBy('day_of_week')->orderBy('start_time')->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $timeSlots = [
            '06:00 - 06:30', '06:30 - 07:00', '07:00 - 07:30', '07:30 - 08:00',
            '08:00 - 08:30', '08:30 - 09:00', '09:00 - 09:30', '09:30 - 10:00',
            '10:00 - 10:30', '10:30 - 11:00', '11:00 - 11:30', '11:30 - 12:00',
            '12:00 - 12:30', '12:30 - 13:00', '13:00 - 13:30', '13:30 - 14:00',
            '14:00 - 14:30', '14:30 - 15:00', '15:00 - 15:30', '15:30 - 16:00',
            '16:00 - 17:00',
        ];

        $programs = Program::orderBy('name')->get();
        $myCourses = Course::where('instructor_id', $faculty->id)->orderBy('course_code')->get();

        return view('faculty.timetables.index', compact(
            'slots',
            'days',
            'timeSlots',
            'scope',
            'type',
            'programId',
            'yearOfStudy',
            'dayOfWeek',
            'programs',
            'myCourses'
        ));
    }
}
