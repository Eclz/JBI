<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendanceData = Attendance::where('student_id', Auth::id())
            ->with('course')
            ->selectRaw('course_id,
                COUNT(*) as total_classes,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count')
            ->groupBy('course_id')
            ->get();

        return view('student.attendance.index', compact('attendanceData'));
    }

    public function show(Course $course)
    {
        // Check if student is enrolled
        $course->enrollments()
            ->where('student_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $attendanceRecords = Attendance::where('student_id', Auth::id())
            ->where('course_id', $course->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        $attendanceStats = [
            'total' => $attendanceRecords->total(),
            'present' => Attendance::where('student_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('status', 'present')
                ->count(),
            'late' => Attendance::where('student_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('status', 'late')
                ->count(),
            'absent' => Attendance::where('student_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('status', 'absent')
                ->count(),
        ];

        return view('student.attendance.show', compact('course', 'attendanceRecords', 'attendanceStats'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $qrData = json_decode($request->qr_data, true);

        if (!$qrData || !isset($qrData['course_id'], $qrData['date'], $qrData['token'])) {
            return back()->withErrors(['error' => 'Invalid QR code.']);
        }

        // Verify token
        $expectedToken = md5($qrData['course_id'] . $qrData['date'] . config('app.key'));
        if ($qrData['token'] !== $expectedToken) {
            return back()->withErrors(['error' => 'Invalid or expired QR code.']);
        }

        // Check if student is enrolled in the course
        $course = Course::findOrFail($qrData['course_id']);
        $enrollment = $course->enrollments()
            ->where('student_id', Auth::id())
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return back()->withErrors(['error' => 'You are not enrolled in this course.']);
        }

        // Mark attendance
        Attendance::updateOrCreate(
            [
                'student_id' => Auth::id(),
                'course_id' => $course->id,
                'date' => $qrData['date'],
            ],
            [
                'status' => 'present',
                'marked_by' => Auth::id(),
            ]
        );

        return back()->with('success', 'Attendance marked successfully.');
    }

    // Additional methods can be added here if needed
}
