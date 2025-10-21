<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentProfileRequest;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Department;
use App\Models\CourseEnrollment;
use App\Models\Grade;
use App\Models\FeeRecord;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', StudentProfile::class);

        $students = User::with(['studentProfile.department'])
            ->where('role', 'student')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('studentProfile', function ($sq) use ($search) {
                          $sq->where('student_id', 'like', "%{$search}%");
                      });
                });
            })
            ->when(request('department'), function ($query, $department) {
                $query->whereHas('studentProfile', function ($q) use ($department) {
                    $q->where('department_id', $department);
                });
            })
            ->when(request('status'), function ($query, $status) {
                $query->whereHas('studentProfile', function ($q) use ($status) {
                    $q->where('academic_status', $status);
                });
            })
            ->paginate(20);

        $departments = Department::where('is_active', true)->get();

        return view('students.index', compact('students', 'departments'));
    }

    public function show(User $student)
    {
        $this->authorize('view', $student->studentProfile);

        $student->load([
            'studentProfile.department',
            'enrollments.course',
            'grades.course',
            'feeRecords',
            'attendanceRecords.course'
        ]);

        $currentEnrollments = $student->enrollments()
            ->whereHas('course.semester', function ($q) {
                $q->where('is_current', true);
            })
            ->with('course.semester')
            ->get();

        $academicSummary = [
            'total_credits' => $student->studentProfile->total_credits_earned,
            'current_gpa' => $student->studentProfile->current_gpa,
            'cumulative_gpa' => $student->studentProfile->cumulative_gpa,
            'academic_status' => $student->studentProfile->academic_status,
        ];

        $financialSummary = [
            'total_fees' => $student->feeRecords->sum('amount'),
            'paid_amount' => $student->feeRecords->sum('paid_amount'),
            'outstanding' => $student->feeRecords->sum('amount') - $student->feeRecords->sum('paid_amount'),
        ];

        return view('students.show', compact(
            'student',
            'currentEnrollments',
            'academicSummary',
            'financialSummary'
        ));
    }

    public function create()
    {
        $this->authorize('create', StudentProfile::class);

        $departments = Department::where('is_active', true)->get();
        return view('students.create', compact('departments'));
    }

    public function store(StoreStudentProfileRequest $request)
    {
        $this->authorize('create', StudentProfile::class);

        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'student',
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'is_active' => true,
            ]);

            StudentProfile::create([
                'user_id' => $user->id,
                'student_id' => $request->student_id,
                'department_id' => $request->department_id,
                'program' => $request->program,
                'admission_date' => $request->admission_date,
                'expected_graduation_date' => $request->expected_graduation_date,
                'academic_status' => 'active',
                'guardian_name' => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'guardian_email' => $request->guardian_email,
            ]);

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create student.']);
        }
    }

    public function edit(User $student)
    {
        $this->authorize('update', $student->studentProfile);

        $departments = Department::where('is_active', true)->get();
        return view('students.edit', compact('student', 'departments'));
    }

    public function update(StoreStudentProfileRequest $request, User $student)
    {
        $this->authorize('update', $student->studentProfile);

        DB::beginTransaction();

        try {
            $student->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
            ]);

            $student->studentProfile->update([
                'department_id' => $request->department_id,
                'program' => $request->program,
                'expected_graduation_date' => $request->expected_graduation_date,
                'academic_status' => $request->academic_status,
                'guardian_name' => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'guardian_email' => $request->guardian_email,
            ]);

            DB::commit();

            return redirect()->route('students.show', $student)
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update student.']);
        }
    }

    public function destroy(User $student)
    {
        $this->authorize('delete', $student->studentProfile);

        $student->update(['is_active' => false]);

        return redirect()->route('students.index')
            ->with('success', 'Student deactivated successfully.');
    }

    public function transcript(User $student)
    {
        $this->authorize('view', $student->studentProfile);

        $student->load([
            'studentProfile.department',
            'grades.course.semester',
            'grades' => function ($query) {
                $query->orderBy('created_at');
            }
        ]);

        $transcriptData = $student->grades->groupBy('course.semester.name');

        return view('students.transcript', compact('student', 'transcriptData'));
    }
}
