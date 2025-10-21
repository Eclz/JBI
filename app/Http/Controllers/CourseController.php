<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['department', 'semester', 'instructor'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(request('department'), function ($query, $department) {
                $query->where('department_id', $department);
            })
            ->when(request('semester'), function ($query, $semester) {
                $query->where('semester_id', $semester);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        
        return view('courses.index', compact('courses', 'departments', 'semesters'));
    }

    public function show(Course $course)
    {
        $course->load([
            'department',
            'semester',
            'instructor',
            'enrollments.student',
            'assignments',
            'materials',
            'attendanceRecords'
        ]);

        $enrollmentStats = [
            'total_enrolled' => $course->enrollments->count(),
            'capacity' => $course->capacity,
            'available_spots' => $course->capacity - $course->enrollments->count(),
        ];

        $gradeDistribution = $course->grades()
            ->selectRaw('grade, COUNT(*) as count')
            ->groupBy('grade')
            ->pluck('count', 'grade')
            ->toArray();

        return view('courses.show', compact('course', 'enrollmentStats', 'gradeDistribution'));
    }

    public function create()
    {
        $this->authorize('create', Course::class);
        
        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $instructors = User::where('role', 'faculty')
            ->where('is_active', true)
            ->get();
        
        return view('courses.create', compact('departments', 'semesters', 'instructors'));
    }

    public function store(StoreCourseRequest $request)
    {
        $this->authorize('create', Course::class);
        
        $course = Course::create($request->validated());
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        
        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $instructors = User::where('role', 'faculty')
            ->where('is_active', true)
            ->get();
        
        return view('courses.edit', compact('course', 'departments', 'semesters', 'instructors'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->authorize('update', $course);
        
        $course->update($request->validated());
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        
        if ($course->enrollments()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete course with enrolled students.']);
        }
        
        $course->delete();
        
        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function enroll(Request $request, Course $course)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($request->student_id);
        
        if ($course->enrollments()->where('student_id', $student->id)->exists()) {
            return back()->withErrors(['error' => 'Student is already enrolled in this course.']);
        }

        if ($course->enrollments()->count() >= $course->capacity) {
            return back()->withErrors(['error' => 'Course is at full capacity.']);
        }

        CourseEnrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => now(),
            'status' => 'enrolled',
        ]);

        return back()->with('success', 'Student enrolled successfully.');
    }

    public function unenroll(Course $course, User $student)
    {
        $enrollment = $course->enrollments()
            ->where('student_id', $student->id)
            ->first();

        if (!$enrollment) {
            return back()->withErrors(['error' => 'Student is not enrolled in this course.']);
        }

        $enrollment->update(['status' => 'dropped']);

        return back()->with('success', 'Student unenrolled successfully.');
    }
}
