<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentProfileRequest;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Department;
use App\Models\Program;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\StudentNote;
use App\Models\Attendance;
use App\Models\FeeRecord;
use App\Models\Grade;
use App\Models\Assignment;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')
            ->with(['studentProfile.department'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('studentProfile', function ($sq) use ($search) {
                          $sq->where('admission_number', 'like', "%{$search}%")
                            ->orWhere('student_id', 'like', "%{$search}%");
                      });
                });
            })
            ->when(request('department'), function ($query, $department) {
                $query->whereHas('studentProfile', function ($q) use ($department) {
                    $q->where('department_id', $department);
                });
            })
            ->when(request('status'), function ($query, $status) {
                if ($status === 'active') {
                    $query->where('is_active', true)
                          ->whereHas('studentProfile', function ($q) {
                              $q->where('status', 'active');
                          });
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                } elseif (in_array($status, ['graduated', 'suspended', 'dropped'])) {
                    $query->whereHas('studentProfile', function ($q) use ($status) {
                        $q->where('status', $status);
                    });
                }
            })
            ->when(request('semester'), function ($query, $semester) {
                $query->whereHas('studentProfile', function ($q) use ($semester) {
                    $q->where('current_semester', $semester);
                });
            })
            ->when(request('year_of_study'), function ($query, $year) {
                $query->whereHas('studentProfile', function ($q) use ($year) {
                    $q->where('year_of_study', $year);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $departments = Department::where('is_active', true)->get();

        return view('admin.students.index', compact('students', 'departments'));
    }

    public function show(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $student->load([
            'studentProfile.department',
            'enrolledCourses.department',
            'enrolledCourses.instructor',
            'grades.course',
            'attendanceRecords.course',
            'feeRecords.feeStructure',
            'studentNotes' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        $stats = [
            'total_courses' => $student->enrolledCourses->count(),
            'active_courses' => $student->enrolledCourses->where('pivot.status', 'enrolled')->count(),
            'completed_courses' => $student->enrolledCourses->where('pivot.status', 'completed')->count(),
            'total_credits' => $student->enrolledCourses()
                ->wherePivot('status', 'completed')
                ->sum('credits'),
            'gpa' => $this->calculateGPA($student),
        ];

        return view('admin.students.show', compact('student', 'stats'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $programs = Program::where('is_active', true)->with(['department', 'level'])->orderBy('name')->get();
        $nextAdmissionNumber = $this->generateAdmissionNumber();
        return view('admin.students.create', compact('departments', 'programs', 'nextAdmissionNumber'));
    }

    public function getNextAdmissionNumber(Request $request)
    {
        $departmentId = $request->query('department_id');
        $admissionNumber = $this->generateAdmissionNumber($departmentId);

        return response()->json([
            'admission_number' => $admissionNumber
        ]);
    }

    public function store(StoreStudentProfileRequest $request)
    {
        DB::beginTransaction();

        try {
            $firstName = trim($request->input('first_name', ''));
            $lastName = trim($request->input('last_name', ''));
            $fullName = trim($firstName . ' ' . $lastName);

            // Create user
            $user = User::create([
                'first_name' => $firstName ?: null,
                'last_name' => $lastName ?: null,
                'name' => $fullName ?: $request->input('name'),
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'password123'),
                'role' => 'student',
                'is_active' => true,
                'must_change_password' => true,
            ]);

            // Generate admission number
            $admissionNumber = $this->generateAdmissionNumber($request->department_id);

            // Create student profile
            $program = $request->program_id ? Program::find($request->program_id) : null;
            $resolvedDepartmentId = $program?->department_id ?? $request->department_id;

            $studentProfile = StudentProfile::create([
                'user_id' => $user->id,
                'student_id' => $request->student_id ?? $this->generateStudentId(),
                'admission_number' => $admissionNumber,
                'department_id' => $resolvedDepartmentId,
                'program_id' => $program?->id,
                'program' => $program?->name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'admission_date' => $request->admission_date ?? now(),
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('admin.students.show', $user)
                ->with('success', 'Student created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create student. Please try again.'])->withInput();
        }
    }

    public function edit(User $student)
    {

        if ($student->role !== 'student') {
            abort(404);
        }

        $student->load('studentProfile');
        $departments = Department::where('is_active', true)->get();
        $programsQuery = Program::with(['department', 'level'])->orderBy('name');

        if ($student->studentProfile?->program_id) {
            $programsQuery->where(function ($query) use ($student) {
                $query->where('is_active', true)
                    ->orWhere('id', $student->studentProfile->program_id);
            });
        } else {
            $programsQuery->where('is_active', true);
        }
        $programs = $programsQuery->get();
        $nextAdmissionNumber = $this->generateAdmissionNumber($student->studentProfile?->department_id);

        return view('admin.students.edit', compact('student', 'departments', 'programs', 'nextAdmissionNumber'));
    }

    public function update(StoreStudentProfileRequest $request, User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $firstName = trim($request->input('first_name', ''));
            $lastName = trim($request->input('last_name', ''));
            $fullName = trim($firstName . ' ' . $lastName);

            // Update user
            $student->update([
                'first_name' => $firstName ?: $student->first_name,
                'last_name' => $lastName ?: $student->last_name,
                'name' => $fullName ?: $request->input('name', $student->name),
                'email' => $request->email,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $student->update([
                    'password' => Hash::make($request->password),
                    'must_change_password' => true,
                ]);
            }

            // Update student profile
            $program = $request->program_id ? Program::find($request->program_id) : null;
            $resolvedDepartmentId = $program?->department_id ?? $request->department_id;

            $student->studentProfile->update([
                'student_id' => $request->student_id,
                'department_id' => $resolvedDepartmentId,
                'program_id' => $program?->id,
                'program' => $program?->name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'admission_date' => $request->admission_date,
                'status' => $request->status ?? 'active',
            ]);

            DB::commit();

            return redirect()->route('admin.students.show', $student)
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update student. Please try again.'])->withInput();
        }
    }

    public function destroy(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        // Check if student has enrollments
        if (CourseEnrollment::where('user_id', $student->id)->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete student with course enrollments.']);
        }

        DB::beginTransaction();

        try {
            $student->studentProfile()->delete();
            $student->delete();

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete student. Please try again.']);
        }
    }

    public function showEnrollCourse(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $student->load('studentProfile.department');

        // Get available courses (not already enrolled)
        $availableCourses = Course::where('status', 'active')
            ->whereNotIn('id', function($query) use ($student) {
                $query->select('course_id')
                      ->from('course_enrollments')
                      ->where('user_id', $student->id)
                      ->where('status', '!=', 'dropped');
            })
            ->with(['department', 'instructor'])
            ->get();

        return view('admin.students.enroll-course', compact('student', 'availableCourses'));
    }

    public function enrollCourse(Request $request, User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Check if already enrolled
        if (CourseEnrollment::where('user_id', $student->id)->where('course_id', $course->id)->where('status', '!=', 'dropped')->exists()) {
            return back()->withErrors(['error' => 'Student is already enrolled in this course.']);
        }

        // Check course capacity
        if ($course->capacity && $course->activeEnrollments()->count() >= $course->capacity) {
            return back()->withErrors(['error' => 'Course is at full capacity.']);
        }

        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => now(),
            'status' => 'enrolled',
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student enrolled in course successfully.');
    }

    public function academicRecord(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $student->load([
            'studentProfile.department',
            'enrolledCourses.department',
        ]);

        // Get enrollments with courses
        $enrollments = CourseEnrollment::where('user_id', $student->id)
            ->with('course.department', 'course.semester')
            ->orderBy('enrollment_date', 'desc')
            ->get();

        // Get grades for all enrollments
        $grades = Grade::where('user_id', $student->id)
            ->with('assignment.course')
            ->get();

        // Calculate semester-wise GPA
        $semesterGPAs = [];
        foreach ($enrollments as $enrollment) {
            $semesterId = $enrollment->course->semester_id;
            if (!isset($semesterGPAs[$semesterId])) {
                $semesterGPAs[$semesterId] = [
                    'semester' => $enrollment->course->semester,
                    'courses' => [],
                    'total_credits' => 0,
                    'total_points' => 0,
                    'gpa' => 0,
                ];
            }

            $courseGrades = $grades->where('assignment.course_id', $enrollment->course_id);
            $courseGPA = $this->calculateCourseGPA($courseGrades);

            $semesterGPAs[$semesterId]['courses'][] = [
                'course' => $enrollment->course,
                'grade' => $courseGPA,
                'credits' => $enrollment->course->credits,
            ];

            $semesterGPAs[$semesterId]['total_credits'] += $enrollment->course->credits;
            $semesterGPAs[$semesterId]['total_points'] += $courseGPA * $enrollment->course->credits;
        }

        // Calculate GPA for each semester
        foreach ($semesterGPAs as $key => $semester) {
            if ($semester['total_credits'] > 0) {
                $semesterGPAs[$key]['gpa'] = $semester['total_points'] / $semester['total_credits'];
            }
        }

        return view('admin.students.academic-record', compact('student', 'grades', 'semesterGPAs'));
    }

    public function attendance(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $student->load('studentProfile.department');

        $attendanceRecords = Attendance::where('user_id', $student->id)
            ->with('course')
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        // Calculate attendance statistics
        $stats = [
            'total_classes' => Attendance::where('user_id', $student->id)->count(),
            'present' => Attendance::where('user_id', $student->id)->where('status', 'present')->count(),
            'absent' => Attendance::where('user_id', $student->id)->where('status', 'absent')->count(),
            'late' => Attendance::where('user_id', $student->id)->where('status', 'late')->count(),
        ];

        $stats['attendance_percentage'] = $stats['total_classes'] > 0
            ? round(($stats['present'] / $stats['total_classes']) * 100, 2)
            : 0;

        return view('admin.students.attendance', compact('student', 'attendanceRecords', 'stats'));
    }

    public function fees(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $student->load('studentProfile.department');

        $feeRecords = FeeRecord::where('user_id', $student->id)
            ->with('feeStructure')
            ->orderBy('due_date', 'desc')
            ->paginate(20);

        $allFeeRecords = FeeRecord::where('user_id', $student->id)->get();

        // Calculate fee statistics
        $stats = [
            'total_fees' => $allFeeRecords->sum('amount'),
            'paid_fees' => $allFeeRecords->where('status', 'paid')->sum('amount'),
            'pending_fees' => $allFeeRecords->where('status', 'pending')->sum('amount'),
            'overdue_fees' => $allFeeRecords->where('status', 'overdue')->sum('amount'),
        ];

        return view('admin.students.fees', compact('student', 'feeRecords', 'stats'));
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();

            return back()->with('success',
                "Import completed! Created: {$results['created']}, Updated: {$results['updated']}, Errors: {$results['errors']}"
            );

        } catch (\Exception $e) {
            Log::error('Bulk import error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new StudentsExport($request->all()), 'students.xlsx');
    }

    private function generateAdmissionNumber($departmentId = null)
    {
        $year = date('Y');
        $department = $departmentId ? Department::find($departmentId) : null;
        $prefix = $department && !empty($department->code) ? strtoupper(substr($department->code, 0, 3)) : 'JBI';

        $profiles = StudentProfile::where('admission_number', 'like', "{$prefix}{$year}%")
            ->pluck('admission_number');

        $applications = Application::where('admission_number', 'like', "{$prefix}{$year}%")
            ->pluck('admission_number');

        $allNumbers = $profiles->concat($applications);

        $maxSequence = 0;
        foreach ($allNumbers as $adm) {
            if ($adm && preg_match('/^' . preg_quote($prefix . $year, '/') . '(\d+)$/', $adm, $matches)) {
                $seq = intval($matches[1]);
                if ($seq > $maxSequence) {
                    $maxSequence = $seq;
                }
            }
        }

        $newSequence = $maxSequence + 1;
        return $prefix . $year . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    private function generateStudentId()
    {
        do {
            $studentId = 'STU' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (StudentProfile::where('student_id', $studentId)->exists());

        return $studentId;
    }

    private function calculateGPA($student)
    {
        $enrollments = CourseEnrollment::where('user_id', $student->id)
            ->where('status', 'completed')
            ->with('course')
            ->get();

        if ($enrollments->isEmpty()) {
            return 0;
        }

        $totalCredits = 0;
        $totalPoints = 0;

        foreach ($enrollments as $enrollment) {
            if ($enrollment->grade_points && $enrollment->course->credits) {
                $totalCredits += $enrollment->course->credits;
                $totalPoints += $enrollment->grade_points * $enrollment->course->credits;
            }
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }

    private function calculateCourseGPA($grades)
    {
        if ($grades->isEmpty()) {
            return 0;
        }

        $totalPoints = $grades->sum('points_earned');
        $maxPoints = $grades->sum(function ($grade) {
            return $grade->assignment->max_points;
        });

        if ($maxPoints == 0) {
            return 0;
        }

        $percentage = ($totalPoints / $maxPoints) * 100;

        // Convert percentage to GPA (4.0 scale)
        if ($percentage >= 90) return 4.0;
        if ($percentage >= 80) return 3.0;
        if ($percentage >= 70) return 2.0;
        if ($percentage >= 60) return 1.0;
        return 0.0;
    }
}
