<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    public function index()
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->with(['semester', 'enrollments'])
            ->get();

        return view('faculty.attendance.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);
        
        $attendanceRecords = Attendance::where('course_id', $course->id)
            ->with('student')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('faculty.attendance.show', compact('course', 'attendanceRecords'));
    }

    public function mark(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late',
        ]);

        foreach ($request->attendance as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'course_id' => $course->id,
                    'date' => $request->date,
                ],
                [
                    'status' => $status,
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return back()->with('success', 'Attendance marked successfully.');
    }

    public function generateQRCode(Course $course)
    {
        $this->authorize('view', $course);
        
        $qrData = [
            'course_id' => $course->id,
            'date' => now()->toDateString(),
            'token' => md5($course->id . now()->toDateString() . config('app.key'))
        ];

        $qrCode = QrCode::size(300)->generate(json_encode($qrData));

        return view('faculty.attendance.qr-code', compact('course', 'qrCode'));
    }
}
