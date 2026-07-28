<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['department', 'semester', 'instructor'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(request('department'), function ($query, $department) {
                $query->where('department_id', $department);
            })
            ->when(request('semester'), function ($query, $semester) {
                $query->where('semester_id', $semester);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        return view('admin.courses.index', compact('courses', 'departments', 'semesters'));
    }

    public function show(Course $course)
    {
        $course->load([
            'department',
            'semester',
            'instructor',
            'enrollments.student',
            'assignments',
            'materials'
        ]);

        return view('admin.courses.show', compact('course'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $instructors = User::where('role', 'faculty')->where('is_active', true)->get();
        $programs = Program::where('is_active', true)->with(['department', 'level'])->orderBy('name')->get();

        return view('admin.courses.create', compact('departments', 'semesters', 'instructors', 'programs'));
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $instructors = User::where('role', 'faculty')->where('is_active', true)->get();
        $programs = Program::where('is_active', true)->with(['department', 'level'])->orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'departments', 'semesters', 'instructors', 'programs'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if ($course->enrollments()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete course with enrolled students.']);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function enrollments(Course $course)
    {
        $enrollments = $course->enrollments()
            ->with('student.studentProfile.department')
            ->orderBy('enrollment_date', 'desc')
            ->paginate(20);

        // Get available students for enrollment (not already enrolled)
        $availableStudents = User::where('role', 'student')
            ->where('is_active', true)
            ->whereNotIn('id', function($query) use ($course) {
                $query->select('user_id')
                      ->from('course_enrollments')
                      ->where('course_id', $course->id)
                      ->where('status', '!=', 'dropped');
            })
            ->with('studentProfile')
            ->orderBy('name')
            ->get();

        // Add missing variables that the view expects
        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $instructors = User::where('role', 'faculty')->where('is_active', true)->get();

        return view('admin.courses.enrollments', compact('course', 'enrollments', 'availableStudents', 'departments', 'semesters', 'instructors'));
    }

    public function enrollStudent(Request $request, Course $course)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($request->student_id);

        // Check if student is already enrolled
        if ($course->enrollments()->where('user_id', $student->id)->where('status', '!=', 'dropped')->exists()) {
            return back()->withErrors(['error' => 'Student is already enrolled in this course.']);
        }

        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => now(),
            'status' => 'enrolled',
        ]);

        return back()->with('success', 'Student enrolled successfully.');
    }

    public function dropStudent(Course $course, CourseEnrollment $enrollment)
    {
        // Ensure the enrollment belongs to this course
        if ($enrollment->course_id !== $course->id) {
            return back()->withErrors(['error' => 'Invalid enrollment for this course.']);
        }

        $enrollment->update(['status' => 'dropped']);

        return back()->with('success', 'Student dropped from course successfully.');
    }

    public function materials(Course $course)
    {
        $materials = $course->materials()
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('admin.courses.materials', compact('course', 'materials'));
    }

    public function storeMaterial(Request $request, Course $course)
    {
        Log::info('Material upload attempt', [
            'course_id' => $course->id,
            'request_data' => $request->except(['file']),
            'has_file' => $request->hasFile('file'),
            'file_info' => $request->hasFile('file') ? [
                'name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'mime' => $request->file('file')->getMimeType(),
            ] : null
        ]);

        // Check if storage directory exists and is writable
        $storagePath = storage_path('app/public/course_materials');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Dynamic validation rules based on material type
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|in:document,video,audio,image,link',
            'description' => 'nullable|string|max:1000',
            'is_downloadable' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ];

        // Add conditional validation based on type
        if ($request->input('type') === 'link') {
            $rules['link_url'] = 'required|url|max:500';
        } else {
            $rules['file'] = 'required|file|max:51200'; // 50MB max

            // Add specific file type validation based on material type
            switch ($request->input('type')) {
                case 'document':
                    $rules['file'] .= '|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,rtf';
                    break;
                case 'video':
                    $rules['file'] .= '|mimes:mp4,avi,mov,wmv,flv,webm';
                    break;
                case 'audio':
                    $rules['file'] .= '|mimes:mp3,wav,ogg,m4a,aac';
                    break;
                case 'image':
                    $rules['file'] .= '|mimes:jpg,jpeg,png,gif,bmp,svg,webp';
                    break;
            }
        }

        // Custom error messages
        $messages = [
            'title.required' => 'The material title is required.',
            'title.max' => 'The material title cannot exceed 255 characters.',
            'type.required' => 'Please select a material type.',
            'type.in' => 'Invalid material type selected.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'The file size cannot exceed 50MB.',
            'file.mimes' => 'Invalid file type for the selected material type.',
            'link_url.required' => 'The URL is required for link materials.',
            'link_url.url' => 'Please enter a valid URL.',
            'link_url.max' => 'The URL cannot exceed 500 characters.',
            'order.integer' => 'The order must be a number.',
            'order.min' => 'The order cannot be negative.',
        ];

        try {
            // Validate the request
            $validated = $request->validate($rules, $messages);

            Log::info('Validation passed', ['validated_data' => $validated]);

            // Prepare material data
            $materialData = [
                'course_id' => $course->id,
                'title' => $validated['title'],
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'is_downloadable' => $request->boolean('is_downloadable', true),
                'is_published' => true,
                'order' => $validated['order'] ?? 0,
                'uploaded_by' => auth()->id(),
            ];

            if ($validated['type'] === 'link') {
                // Handle external link
                $materialData['external_url'] = $validated['link_url'];
                $materialData['file_path'] = null;

                Log::info('Processing link material', ['url' => $validated['link_url']]);
            } else {
                // Handle file upload
                if (!$request->hasFile('file')) {
                    Log::error('No file found in request');
                    return back()->withErrors(['file' => 'File upload is required for this material type.']);
                }

                $file = $request->file('file');

                if (!$file->isValid()) {
                    Log::error('Invalid file upload', ['error' => $file->getErrorMessage()]);
                    return back()->withErrors(['file' => 'File upload failed. Please try again.']);
                }

                // Generate unique filename
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;

                // Create course-specific directory
                $courseDir = 'course_materials/' . $course->id;

                try {
                    // Store file
                    $path = $file->storeAs($courseDir, $filename, 'public');

                    if (!$path) {
                        Log::error('File storage failed');
                        return back()->withErrors(['file' => 'Failed to store file. Please try again.']);
                    }

                    $materialData['file_path'] = $path;
                    $materialData['file_name'] = $file->getClientOriginalName();
                    $materialData['file_type'] = $file->getClientMimeType();
                    $materialData['file_size'] = $file->getSize();

                    Log::info('File stored successfully', [
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime' => $file->getClientMimeType()
                    ]);

                } catch (\Exception $e) {
                    Log::error('File storage exception', ['error' => $e->getMessage()]);
                    return back()->withErrors(['file' => 'Failed to store file: ' . $e->getMessage()]);
                }
            }

            // Create the material record
            $material = CourseMaterial::create($materialData);

            Log::info('Material created successfully', ['material_id' => $material->id]);

            return back()->with('success', 'Course material added successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Material upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to upload material: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroyMaterial(Course $course, CourseMaterial $material)
    {
        try {
            // Ensure the material belongs to this course
            if ($material->course_id !== $course->id) {
                return back()->withErrors(['error' => 'Invalid material for this course.']);
            }

            // Delete file if it exists and is not a link
            if ($material->type !== 'link' && $material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            $material->delete();

            return back()->with('success', 'Course material deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Course material deletion error', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete material. Please try again.']);
        }
    }

    public function assignments(Course $course)
    {
        $assignments = $course->assignments()
            ->withCount('submissions')
            ->orderBy('due_date', 'desc')
            ->paginate(20);

        return view('admin.courses.assignments', compact('course', 'assignments'));
    }

    public function grades(Course $course)
    {
        $enrollments = $course->enrollments()
            ->with('student.studentProfile')
            ->where('status', 'enrolled')
            ->get();

        $assignments = $course->assignments()
            ->orderBy('due_date', 'asc')
            ->get();

        $grades = \App\Models\Grade::whereIn('assignment_id', $assignments->pluck('id'))
            ->get();

        // Calculate average grade
        $totalPoints = $assignments->sum('max_points');
        $averageGrade = 0;

        if ($totalPoints > 0 && $enrollments->count() > 0) {
            $totalEarned = 0;
            foreach ($enrollments as $enrollment) {
                $studentGrades = $grades->where('student_id', $enrollment->student->id);
                $totalEarned += $studentGrades->sum('points_earned');
            }
            $averageGrade = ($totalEarned / ($totalPoints * $enrollments->count())) * 100;
        }

        // Calculate grade distribution
        $gradeDistribution = [0, 0, 0, 0, 0]; // A, B, C, D, F

        foreach ($enrollments as $enrollment) {
            $studentGrades = $grades->where('student_id', $enrollment->student->id);
            $earnedPoints = $studentGrades->sum('points_earned');
            $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;

            if ($percentage >= 90) $gradeDistribution[0]++;
            elseif ($percentage >= 80) $gradeDistribution[1]++;
            elseif ($percentage >= 70) $gradeDistribution[2]++;
            elseif ($percentage >= 60) $gradeDistribution[3]++;
            else $gradeDistribution[4]++;
        }

        return view('admin.courses.grades', compact(
            'course',
            'enrollments',
            'assignments',
            'grades',
            'averageGrade',
            'gradeDistribution'
        ));
    }

    public function exportGrades(Course $course, Request $request)
    {
        $format = $request->get('format', 'excel');

        // This would typically use a package like Laravel Excel
        // For now, return a simple response
        return response()->json([
            'message' => 'Grade export functionality would be implemented here',
            'format' => $format,
            'course' => $course->name
        ]);
    }
}
