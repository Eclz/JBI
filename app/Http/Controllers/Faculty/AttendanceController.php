<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    public function overview()
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->withCount('enrollments as enrolled_students_count')
            ->with(['semester', 'enrollments'])
            ->get();

        // Calculate attendance statistics per course
        $attendanceStats = [];
        $totalClasses = 0;

        foreach ($courses as $course) {
            $stats = Attendance::where('course_id', $course->id)
                ->selectRaw('
                    COUNT(*) as total_records,
                    SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_count,
                    SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count
                ')
                ->first();

            $attendanceStats[$course->id] = $stats;
            $totalClasses += $stats->total_records ?? 0;
        }

        return view('faculty.attendance.index', compact('courses', 'attendanceStats', 'totalClasses'));
    }

    public function index(Course $course)
    {
        $this->authorize('view', $course);

        $attendanceRecords = Attendance::where('course_id', $course->id)
            ->with(['student', 'markedBy'])
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        return view('faculty.attendance.show', compact('course', 'attendanceRecords'));
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);

        $attendanceRecords = Attendance::where('course_id', $course->id)
            ->with(['student', 'markedBy'])
            ->orderBy('attendance_date', 'desc')
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
                    'user_id' => $studentId,
                    'course_id' => $course->id,
                    'attendance_date' => $request->date,
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
