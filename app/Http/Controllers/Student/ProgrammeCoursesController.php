<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\CourseEnrollment;
use App\Models\FeeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        // Available courses for enrollment
        $program = null;
        if ($studentProfile?->program_id) {
            $program = Program::find($studentProfile->program_id);
        }

        $query = Course::with('faculty');
        if ($program) {
            $query->where(function($q) use ($program) {
                $q->where('program_id', $program->id)
                  ->orWhere('department_id', $program->department_id)
                  ->orWhereNull('program_id');
            });
        }

        $availableCourses = $query->get();

        // Currently enrolled courses for student
        $currentEnrollments = CourseEnrollment::with('course.faculty')
            ->where('user_id', $user->id)
            ->get();

        return view('student.enrollment.index', compact('user', 'studentProfile', 'academicYears', 'semesters', 'availableCourses', 'currentEnrollments'));
    }

    public function processEnrollment(Request $request)
    {
        $request->validate([
            'year_of_study' => 'required|integer|min:1|max:6',
            'current_semester' => 'required|integer|in:1,2',
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
            'enrollment_types' => 'required|array',
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

        $invoicesGenerated = 0;

        foreach ($request->course_ids as $courseId) {
            $type = $request->enrollment_types[$courseId] ?? 'normal';
            $course = Course::find($courseId);

            CourseEnrollment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                ],
                [
                    'status' => 'enrolled',
                    'enrollment_type' => $type,
                    'enrollment_date' => now(),
                ]
            );

            // Generate invoice if retake or missed paper
            if ($type === 'retake') {
                $amount = 150000; // Retake Fee UGX 150,000
                FeeRecord::create([
                    'user_id' => $user->id,
                    'invoice_number' => 'INV-RET-' . strtoupper(Str::random(6)),
                    'amount' => $amount,
                    'total_amount' => $amount,
                    'balance_amount' => $amount,
                    'paid_amount' => 0,
                    'type' => 'retake_fee',
                    'status' => 'unpaid',
                    'due_date' => now()->addDays(30),
                    'payment_notes' => "Automatic Retake Fee Invoice for course: {$course->code} - {$course->title}",
                ]);
                $invoicesGenerated++;
            } elseif ($type === 'missed_paper') {
                $amount = 100000; // Missed Paper Fee UGX 100,000
                FeeRecord::create([
                    'user_id' => $user->id,
                    'invoice_number' => 'INV-MIS-' . strtoupper(Str::random(6)),
                    'amount' => $amount,
                    'total_amount' => $amount,
                    'balance_amount' => $amount,
                    'paid_amount' => 0,
                    'type' => 'missed_paper_fee',
                    'status' => 'unpaid',
                    'due_date' => now()->addDays(30),
                    'payment_notes' => "Automatic Missed Paper Examination Fee Invoice for course: {$course->code} - {$course->title}",
                ]);
                $invoicesGenerated++;
            }
        }

        $msg = 'Successfully enrolled for Year ' . $request->year_of_study . ' Semester ' . $request->current_semester . ' with ' . count($request->course_ids) . ' courses!';
        if ($invoicesGenerated > 0) {
            $msg .= " ({$invoicesGenerated} retake/missed paper fee invoices generated and added to your billing statement).";
        }

        return redirect()->route('student.enrollment.index')->with('success', $msg);
    }

    public function unenroll(Request $request, Course $course)
    {
        $user = Auth::user();

        // Check if enrollment is active for semester
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $enrollment->delete();
            return back()->with('success', "Unenrolled successfully from {$course->code} - {$course->title}.");
        }

        return back()->with('error', "You are not enrolled in {$course->code}.");
    }
}
