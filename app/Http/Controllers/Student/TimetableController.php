<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{
    public function index(Request $request, $type = 'teaching')
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;

        $programId = $studentProfile?->program_id;
        $yearOfStudy = $request->input('year', $studentProfile?->year_of_study ?? 1);
        $semesterNum = $request->input('semester', 1);

        $query = Timetable::with(['course', 'faculty'])
            ->where('type', $type);

        if ($programId) {
            $query->where(function($q) use ($programId) {
                $q->where('program_id', $programId)
                  ->orWhereNull('program_id');
            });
        }

        if ($yearOfStudy) {
            $query->where('year_of_study', $yearOfStudy);
        }

        $slots = $query->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $timeSlots = [
            '06:00 - 06:30',
            '06:30 - 07:00',
            '07:00 - 07:30',
            '07:30 - 08:00',
            '08:00 - 08:30',
            '08:30 - 09:00',
            '09:00 - 09:30',
            '09:30 - 10:00',
            '10:00 - 10:30',
            '10:30 - 11:00',
            '11:00 - 11:30',
            '11:30 - 12:00',
            '12:00 - 12:30',
            '12:30 - 13:00',
            '13:00 - 13:30',
            '13:30 - 14:00',
            '14:00 - 14:30',
            '14:30 - 15:00',
            '15:00 - 15:30',
            '15:30 - 16:00',
            '16:00 - 17:00',
        ];

        return view('student.timetables.index', compact('slots', 'days', 'timeSlots', 'type', 'yearOfStudy', 'semesterNum', 'studentProfile'));
    }

    public function teaching(Request $request)
    {
        return $this->index($request, 'teaching');
    }

    public function tests(Request $request)
    {
        return $this->index($request, 'tests');
    }

    public function exams(Request $request)
    {
        return $this->index($request, 'exams');
    }
}
