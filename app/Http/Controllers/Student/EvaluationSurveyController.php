<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\EvaluationSurvey;
use App\Models\EvaluationResponse;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationSurveyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $surveys = EvaluationSurvey::with(['questions'])
            ->where('is_active', true)
            ->get();

        // Enrolled courses for this student
        $enrolledCourses = $user->enrolledCourses()->with(['faculty'])->get();

        // Get completed responses by student
        $completedResponses = EvaluationResponse::where('student_id', $user->id)
            ->get()
            ->keyBy(function($item) {
                return $item->survey_id . '_' . $item->course_id;
            });

        return view('student.evaluation_surveys.index', compact('surveys', 'enrolledCourses', 'completedResponses'));
    }

    public function show(EvaluationSurvey $survey, Course $course)
    {
        $survey->load('questions');
        $user = Auth::user();

        $existingResponse = EvaluationResponse::where('survey_id', $survey->id)
            ->where('student_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return view('student.evaluation_surveys.show', compact('survey', 'course', 'existingResponse'));
    }

    public function store(Request $request, EvaluationSurvey $survey, Course $course)
    {
        $user = Auth::user();

        $request->validate([
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        EvaluationResponse::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'student_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'lecturer_id' => $course->faculty_id,
                'answers' => $request->ratings,
                'comments' => $request->comments,
                'submitted_at' => now(),
            ]
        );

        return redirect()->route('student.evaluation-surveys.index')->with('success', 'Evaluation survey submitted successfully!');
    }
}
