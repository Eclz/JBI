<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function overview()
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->withCount('materials as materials_count')
            ->with('materials')
            ->get();

        $totalMaterials = $courses->sum('materials_count');

        $recentMaterials = CourseMaterial::whereIn('course_id', $courses->pluck('id'))
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('faculty.materials.index', compact('courses', 'totalMaterials', 'recentMaterials'));
    }

    public function create(Course $course)
    {
        $this->authorize('update', $course);

        return view('faculty.materials.create', compact('course'));
    }

    public function index(Course $course)
    {
        $this->authorize('view', $course);

        $materials = $course->materials()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('faculty.materials.show', compact('course', 'materials'));
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);

        $materials = $course->materials()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('faculty.materials.show', compact('course', 'materials'));
    }

    public function upload(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|in:lecture,reading,assignment,video,other',
        ]);

        $filePath = $request->file('file')->store('course-materials', 'public');

        CourseMaterial::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
            'type' => $request->type,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Material uploaded successfully.');
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|in:lecture,reading,assignment,video,other',
        ]);

        $filePath = $request->file('file')->store('course-materials', 'public');

        CourseMaterial::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
            'type' => $request->type,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('faculty.courses.materials.index', $course)
            ->with('success', 'Material uploaded successfully.');
    }

    public function destroy(CourseMaterial $material)
    {
        $this->authorize('delete', $material);

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return back()->with('success', 'Material deleted successfully.');
    }
}
