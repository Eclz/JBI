<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && ($user->roleCatalog?->slug === 'finance_officer' || $user->hasRole('finance_officer'))) {
                abort(403, 'Finance staff are not authorized to manage course enrollments.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = CourseEnrollment::with(['student.studentProfile', 'student.application', 'course', 'semester']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Filter by enrollment type (normal, retake, missed_paper, redo)
        if ($request->filled('enrollment_type')) {
            $query->where('enrollment_type', $request->enrollment_type);
        }

        // Search by student name, student number / student ID, or email
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('student', function ($q) use ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('student_id', 'like', "%{$search}%")
                      ->orWhereHas('studentProfile', function ($profileQuery) use ($search) {
                          $profileQuery->where('admission_number', 'like', "%{$search}%");
                      })
                      ->orWhereHas('application', function ($appQuery) use ($search) {
                          $appQuery->where('student_number', 'like', "%{$search}%")
                                   ->orWhere('application_number', 'like', "%{$search}%");
                      });
                });
            });
        }

        $enrollments = $query->latest()->paginate(20);

        // Get filter options
        $courses = Course::orderBy('name')->get();
        $semesters = \App\Models\Semester::orderBy('name')->get();

        return view('admin.enrollments.index', compact('enrollments', 'courses', 'semesters'));
    }

    public function create(Request $request)
    {
        $courses = Course::with(['semester', 'department.faculty'])
                        ->where('status', 'active')
                        ->orderBy('name')
                        ->get();

        $selectedCourse = null;
        if ($request->filled('course_id')) {
            $selectedCourse = Course::with('department.faculty')->find($request->course_id);
        }

        $studentsQuery = User::role('student')
                        ->with([
                            'studentProfile.department.faculty',
                            'studentProfile.program.department.faculty',
                            'application.programRecord.department.faculty'
                        ])
                        ->orderBy('name');

        // If a course is selected or specified, filter strictly to students in that course's faculty
        if ($selectedCourse && $selectedCourse->getFacultyId()) {
            $facultyId = $selectedCourse->getFacultyId();
            $studentsQuery->where(function($q) use ($facultyId) {
                $q->whereHas('studentProfile.department', fn($dq) => $dq->where('faculty_id', $facultyId))
                  ->orWhereHas('studentProfile.program.department', fn($dq) => $dq->where('faculty_id', $facultyId))
                  ->orWhereHas('application.programRecord.department', fn($dq) => $dq->where('faculty_id', $facultyId));
            });
        }

        $students = $studentsQuery->get();
        $enrollmentTypes = CourseEnrollment::getEnrollmentTypeOptions();

        return view('admin.enrollments.create', compact('students', 'courses', 'enrollmentTypes', 'selectedCourse'));
    }

    public function facultyStudents(Request $request)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);

        $course = Course::with('department.faculty')->findOrFail($request->course_id);
        $facultyId = $course->getFacultyId();

        $query = User::role('student')
            ->with([
                'studentProfile.department.faculty',
                'studentProfile.program.department.faculty',
                'application.programRecord.department.faculty'
            ])
            ->orderBy('name');

        if ($facultyId) {
            $query->where(function($q) use ($facultyId) {
                $q->whereHas('studentProfile.department', fn($dq) => $dq->where('faculty_id', $facultyId))
                  ->orWhereHas('studentProfile.program.department', fn($dq) => $dq->where('faculty_id', $facultyId))
                  ->orWhereHas('application.programRecord.department', fn($dq) => $dq->where('faculty_id', $facultyId));
            });
        }

        $students = $query->get()->map(function($student) {
            $studentNo = $student->student_number ?? $student->student_id ?? $student->studentProfile?->admission_number ?? $student->application?->student_number ?? 'N/A';
            return [
                'id' => $student->id,
                'name' => $student->name,
                'student_number' => $studentNo,
                'email' => $student->email,
                'faculty_id' => $student->getFacultyId(),
                'faculty_name' => $student->getFacultyName() ?? 'N/A',
            ];
        });

        return response()->json([
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'faculty_id' => $facultyId,
                'faculty_name' => $course->getFacultyName() ?? 'Department / General',
            ],
            'students' => $students,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:enrolled,dropped,completed,pending',
            'enrollment_type' => 'nullable|in:normal,retake,missed_paper,redo',
        ]);

        $userId = $request->student_id;
        $enrollmentType = $request->enrollment_type ?? 'normal';

        // Strictly verify that student belongs to the course's faculty
        $course = Course::with('department.faculty')->findOrFail($request->course_id);
        $courseFacultyId = $course->getFacultyId();

        if ($courseFacultyId) {
            $student = User::with([
                'studentProfile.department.faculty',
                'studentProfile.program.department.faculty',
                'application.programRecord.department.faculty'
            ])->findOrFail($userId);

            $studentFacultyId = $student->getFacultyId();
            if ($studentFacultyId && (int)$studentFacultyId !== (int)$courseFacultyId) {
                return back()->withInput()->with('error', 'Student must strictly belong to the faculty of this course unit (' . ($course->getFacultyName() ?: 'Course Faculty') . ').');
            }
        }

        // Check if enrollment already exists
        $existing = CourseEnrollment::where('user_id', $userId)
                                  ->where('course_id', $request->course_id)
                                  ->first();

        if ($existing) {
            if (in_array($existing->status, ['enrolled', 'pending'])) {
                return back()->with('error', 'Student is already actively enrolled in this course.');
            }

            // Student is redoing, retaking, or sitting missed paper for a previous course unit
            $existing->update([
                'status' => $request->status,
                'enrollment_type' => $enrollmentType,
                'enrollment_date' => $request->enrollment_date,
                'final_grade' => null,
                'letter_grade' => null,
                'grade_points' => null,
                'completion_date' => null,
            ]);

            return redirect()->route('admin.enrollments.index')
                            ->with('success', 'Student enrollment updated successfully as ' . ucfirst(str_replace('_', ' ', $enrollmentType)) . '.');
        }

        CourseEnrollment::create([
            'user_id' => $userId,
            'course_id' => $request->course_id,
            'enrollment_date' => $request->enrollment_date,
            'status' => $request->status,
            'enrollment_type' => $enrollmentType,
        ]);

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Student enrolled successfully (' . ucfirst(str_replace('_', ' ', $enrollmentType)) . ').');
    }

    public function show(CourseEnrollment $enrollment)
    {
        $enrollment->load(['student', 'course', 'semester', 'grades', 'attendances']);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function edit(CourseEnrollment $enrollment)
    {
        $enrollment->load(['student.studentProfile', 'student.application', 'course.semester', 'grades.assignment']);

        $students = User::role('student')
                        ->with(['studentProfile', 'application'])
                        ->orderBy('name')
                        ->get();
        $courses = Course::with('semester')
                        ->orderBy('name')
                        ->get();

        $calculatedResults = $enrollment->calculateStudentResults();
        $semesterEndDate = $enrollment->getSemesterClosureDate();
        $gradingCriteria = CourseEnrollment::getAcademicRegistrarGradingCriteria();
        $enrollmentTypes = CourseEnrollment::getEnrollmentTypeOptions();

        return view('admin.enrollments.edit', compact(
            'enrollment',
            'students',
            'courses',
            'calculatedResults',
            'semesterEndDate',
            'gradingCriteria',
            'enrollmentTypes'
        ));
    }

    public function update(Request $request, CourseEnrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:enrolled,dropped,completed,pending,failed',
            'enrollment_type' => 'nullable|in:normal,retake,missed_paper,redo',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $calculated = $enrollment->calculateStudentResults();
        $semesterEndDate = $enrollment->getSemesterClosureDate();

        // Completion date is automatically the semester end closure date
        $completionDate = ($request->status === 'completed' || $enrollment->completion_date !== null)
            ? ($semesterEndDate ?: $enrollment->completion_date ?: now())
            : null;

        $updateData = [
            'status' => $request->status,
            'enrollment_type' => $request->enrollment_type ?? $enrollment->enrollment_type ?? 'normal',
            'enrollment_date' => $request->enrollment_date,
            'completion_date' => $completionDate,
            'final_grade' => $calculated['final_grade'],
            'letter_grade' => $calculated['letter_grade'],
            'grade_points' => $calculated['grade_points'],
        ];

        if ($request->has('notes')) {
            $updateData['notes'] = $request->notes;
        }

        $enrollment->update($updateData);

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Enrollment updated successfully. Grade points, letter grade, and completion date automatically calculated.');
    }

    public function destroy(CourseEnrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Enrollment deleted successfully.');
    }

    public function approve(CourseEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'enrolled']);

        return back()->with('success', 'Enrollment approved successfully.');
    }

    public function reject(CourseEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'dropped']);

        return back()->with('success', 'Enrollment rejected.');
    }

    public function bulkEnroll(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $enrolled = 0;
        $skipped = 0;

        foreach ($request->student_ids as $studentId) {
            $exists = CourseEnrollment::where('user_id', $studentId)
                                      ->where('course_id', $request->course_id)
                                      ->exists();

            if (!$exists) {
                CourseEnrollment::create([
                    'user_id' => $studentId,
                    'course_id' => $request->course_id,
                    'enrollment_date' => now(),
                    'status' => 'enrolled',
                ]);
                $enrolled++;
            } else {
                $skipped++;
            }
        }

        return back()->with('success', "Enrolled {$enrolled} students. Skipped {$skipped} (already enrolled).");
    }
}
