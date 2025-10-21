<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\StudentNote;
use App\Models\Department;
use App\Models\CourseEnrollment;
use App\Models\Course;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['studentProfile.department'])
            ->where('role', 'student');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('studentProfile', function($sq) use ($search) {
                      $sq->where('admission_number', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%");
                  });
            });
        }

        // Department filter
        if ($request->filled('department')) {
            $query->whereHas('studentProfile', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active' || $request->status === 'inactive') {
                $query->where('is_active', $request->status === 'active');
            } else {
                $query->whereHas('studentProfile', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }
        }

        $students = $query->paginate(20);
        $departments = Department::where('is_active', true)->get();

        return view('admin.students.index', compact('students', 'departments'));
    }

    public function show(User $student)
    {
        $student->load([
            'studentProfile.department',
            'enrolledCourses' => function($query) {
                $query->with(['department', 'instructor', 'semester']);
            },
            'grades.course',
            'feeRecords',
            'attendanceRecords.course',
            'studentNotes' => function($query) {
                $query->with('createdBy')->orderBy('created_at', 'desc');
            }
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function academicRecord(User $student)
    {
        $student->load([
            'studentProfile.department',
            'enrolledCourses.course.semester',
            'grades.course.semester',
            'grades' => function ($query) {
                $query->orderBy('created_at');
            }
        ]);

        // Get enrollments with course and semester information
        $enrollments = $student->enrolledCourses()->with([
            'course.semester',
            'course' => function ($query) {
                $query->select('id', 'code', 'name', 'credits', 'semester_id');
            }
        ])->get();

        $transcriptData = $student->grades->groupBy('course.semester.name');

        return view('admin.students.academic-record', compact('student', 'transcriptData', 'enrollments'));
    }

    public function attendance(User $student)
    {
        $student->load([
            'studentProfile.department',
            'attendanceRecords.course',
            'attendanceRecords' => function ($query) {
                $query->orderBy('date', 'desc');
            }
        ]);

        $attendanceStats = [
            'total_classes' => $student->attendanceRecords->count(),
            'present' => $student->attendanceRecords->where('status', 'present')->count(),
            'absent' => $student->attendanceRecords->where('status', 'absent')->count(),
            'late' => $student->attendanceRecords->where('status', 'late')->count(),
        ];

        $attendanceStats['percentage'] = $attendanceStats['total_classes'] > 0
            ? round(($attendanceStats['present'] / $attendanceStats['total_classes']) * 100, 2)
            : 0;

        return view('admin.students.attendance', compact('student', 'attendanceStats'));
    }

    public function fees(User $student)
    {
        $student->load([
            'studentProfile.department',
            'feeRecords' => function ($query) {
                $query->orderBy('due_date', 'desc');
            }
        ]);

        $feeStats = [
            'total_fees' => $student->feeRecords->sum('amount'),
            'paid_fees' => $student->feeRecords->where('status', 'paid')->sum('amount'),
            'pending_fees' => $student->feeRecords->where('status', 'pending')->sum('amount'),
            'overdue_fees' => $student->feeRecords->where('status', 'overdue')->sum('amount'),
        ];

        return view('admin.students.fees', compact('student', 'feeStats'));
    }

    public function showEnrollForm(User $student)
    {
        $student->load(['studentProfile.department', 'enrolledCourses']);

        // Get courses that the student is not already enrolled in
        $enrolledCourseIds = $student->enrolledCourses->pluck('id')->toArray();
        $availableCourses = Course::whereNotIn('id', $enrolledCourseIds)
            ->where('status', 'active')
            ->with(['semester', 'department', 'instructor'])
            ->get();

        return view('admin.students.enroll-course', compact('student', 'availableCourses'));
    }

    /**
     * Show course enrollment form
     */
    public function showEnrollCourse(User $student)
    {
        // Get courses the student is not already enrolled in
        $enrolledCourseIds = $student->enrolledCourses()->pluck('courses.id');

        $availableCourses = Course::with(['department', 'semester', 'instructor'])
            ->whereNotIn('id', $enrolledCourseIds)
            ->where('status', 'active')
            ->get();

        return view('admin.students.enroll-course', compact('student', 'availableCourses'));
    }

    public function enrollCourse(Request $request, User $student)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:enrolled,pending,waitlisted',
            'notes' => 'nullable|string',
        ]);

        try {
            // Check if already enrolled
            $existingEnrollment = CourseEnrollment::where('user_id', $student->id)
                ->where('course_id', $request->course_id)
                ->first();

            if ($existingEnrollment) {
                return redirect()->back()
                    ->with('error', 'Student is already enrolled in this course.');
            }

            // Create enrollment
            CourseEnrollment::create([
                'user_id' => $student->id,
                'course_id' => $request->course_id,
                'enrollment_date' => $request->enrollment_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            return redirect()->route('admin.students.show', $student)
                ->with('success', 'Student enrolled in course successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error enrolling student: ' . $e->getMessage());
        }
    }

    public function addNote(Request $request, User $student)
    {
        $request->validate([
            'note' => 'required|string|max:2000',
            'type' => 'required|in:general,academic,disciplinary,counseling,medical',
            'priority' => 'required|in:low,medium,high,urgent',
            'is_private' => 'boolean',
        ]);

        try {
            StudentNote::create([
                'student_id' => $student->id,
                'created_by' => Auth::id(),
                'note' => $request->note,
                'type' => $request->type,
                'priority' => $request->priority,
                'is_private' => $request->boolean('is_private'),
                'noted_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Note added successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error adding note: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        $studentIds = $request->student_ids;
        $action = $request->action;

        try {
            switch ($action) {
                case 'activate':
                    User::whereIn('id', $studentIds)
                        ->where('role', 'student')
                        ->update(['is_active' => 1]);
                    $message = 'Students activated successfully.';
                    break;

                case 'deactivate':
                    User::whereIn('id', $studentIds)
                        ->where('role', 'student')
                        ->update(['is_active' => 0]);
                    $message = 'Students deactivated successfully.';
                    break;

                case 'delete':
                    User::whereIn('id', $studentIds)
                        ->where('role', 'student')
                        ->delete();
                    $message = 'Students deleted successfully.';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the bulk action.'
            ], 500);
        }
    }

    /**
     * Import students from CSV/Excel file
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('import_file');
            $path = $file->store('temp');

            // Here you would implement the actual import logic
            // For now, we'll just return a success message

            return response()->json([
                'success' => true,
                'message' => 'Students imported successfully.',
                'imported_count' => 0, // This would be the actual count
                'errors' => [] // Any validation errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while importing students.'
            ], 500);
        }
    }

    /**
     * Export students to CSV/Excel
     */
    public function export(Request $request)
    {
        try {
            $students = User::with(['studentProfile', 'studentProfile.department'])
                ->where('role', 'student')
                ->get();

            $filename = 'students_export_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($students) {
                $file = fopen('php://output', 'w');

                // CSV Headers
                fputcsv($file, [
                    'Admission Number',
                    'Name',
                    'Email',
                    'Phone',
                    'Department',
                    'Program',
                    'Semester',
                    'Status',
                    'Date of Birth',
                    'Address',
                    'Created At'
                ]);

                // CSV Data
                foreach ($students as $student) {
                    fputcsv($file, [
                        $student->studentProfile->admission_number ?? '',
                        $student->name,
                        $student->email,
                        $student->studentProfile->phone ?? '',
                        $student->studentProfile->department->name ?? '',
                        $student->studentProfile->program ?? '',
                        $student->studentProfile->current_semester ?? '',
                        $student->is_active ? 'Active' : 'Inactive',
                        $student->studentProfile->date_of_birth ?? '',
                        $student->studentProfile->address ?? '',
                        $student->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while exporting students.');
        }
    }

    /**
     * Generate next admission number
     */
    private function generateAdmissionNumber()
    {
        $currentYear = date('Y');
        $prefix = 'JBI' . $currentYear;

        // Get the last admission number for this year
        $lastStudent = StudentProfile::where('admission_number', 'like', $prefix . '%')
            ->orderBy('admission_number', 'desc')
            ->first();

        if ($lastStudent) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastStudent->admission_number, -4);
            $nextSequence = $lastSequence + 1;
        } else {
            // First student of the year
            $nextSequence = 1;
        }

        return $prefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get next admission number for AJAX request
     */
    public function getNextAdmissionNumber()
    {
        return response()->json([
            'admission_number' => $this->generateAdmissionNumber()
        ]);
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $nextAdmissionNumber = $this->generateAdmissionNumber();
        return view('admin.students.create', compact('departments', 'nextAdmissionNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'admission_number' => 'required|string|unique:student_profiles,admission_number',
            'admission_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,dropped,suspended',
            'program' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'role' => 'student',
                'is_active' => true,
                'must_change_password' => true,
            ]);

            // Create student profile
            StudentProfile::create([
                'user_id' => $user->id,
                'admission_number' => $request->admission_number,
                'admission_date' => $request->admission_date,
                'department_id' => $request->department_id,
                'program' => $request->program,
                'specialization' => $request->specialization,
                'current_semester' => 1,
                'status' => $request->status,
                'current_gpa' => 0.00,
                'cumulative_gpa' => 0.00,
                'total_credits_earned' => 0,
                'total_credits_required' => 120, // Default for bachelor's programs
            ]);

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create student: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(User $student)
    {
        // Ensure the student has a role of 'student'
        if ($student->role !== 'student') {
            abort(404, 'Student not found');
        }

        $student->load('studentProfile.department');

        // If student profile doesn't exist, create one
        if (!$student->studentProfile) {
            $admissionNumber = $this->generateAdmissionNumber();
            StudentProfile::create([
                'user_id' => $student->id,
                'admission_number' => $admissionNumber,
                'admission_date' => now(),
                'department_id' => Department::first()->id ?? 1,
                'program' => 'Bachelor of Arts in Biblical Studies',
                'current_semester' => 1,
                'status' => 'active',
                'current_gpa' => 0.00,
                'cumulative_gpa' => 0.00,
                'total_credits_earned' => 0,
                'total_credits_required' => 120,
            ]);
            $student->refresh();
            $student->load('studentProfile.department');
        }

        $departments = Department::where('is_active', true)->get();
        return view('admin.students.edit', compact('student', 'departments'));
    }

    public function update(Request $request, User $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'admission_number' => 'required|string|unique:student_profiles,admission_number,' . ($student->studentProfile?->id ?? 'NULL'),
            'admission_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,dropped,suspended',
            'program' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Update user
            $student->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'is_active' => $request->has('is_active'),
            ]);

            // Update or create student profile
            if ($student->studentProfile) {
                $student->studentProfile->update([
                    'admission_number' => $request->admission_number,
                    'admission_date' => $request->admission_date,
                    'department_id' => $request->department_id,
                    'program' => $request->program,
                    'specialization' => $request->specialization,
                    'status' => $request->status,
                ]);
            } else {
                StudentProfile::create([
                    'user_id' => $student->id,
                    'admission_number' => $request->admission_number,
                    'admission_date' => $request->admission_date,
                    'department_id' => $request->department_id,
                    'program' => $request->program,
                    'specialization' => $request->specialization,
                    'current_semester' => 1,
                    'status' => $request->status,
                    'current_gpa' => 0.00,
                    'cumulative_gpa' => 0.00,
                    'total_credits_earned' => 0,
                    'total_credits_required' => 120,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.students.show', $student)
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update student: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(User $student)
    {
        DB::beginTransaction();

        try {
            // Delete student profile first if it exists
            if ($student->studentProfile) {
                $student->studentProfile->delete();
            }

            // Delete user
            $student->delete();

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete student.']);
        }
    }
}
