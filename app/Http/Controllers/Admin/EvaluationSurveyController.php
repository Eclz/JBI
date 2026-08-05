<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationSurvey;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationResponse;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class EvaluationSurveyController extends Controller
{
    public function index()
    {
        $surveys = EvaluationSurvey::with(['questions', 'academicYear'])->withCount('responses')->orderBy('created_at', 'desc')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();

        return view('admin.evaluation_surveys.index', compact('surveys', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_number' => 'required|integer|in:1,2',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $survey = EvaluationSurvey::create($validated);

        // Add standard evaluation questions by default
        $defaultQuestions = [
            ['question_text' => 'Lecturer demonstrates deep knowledge and clarity in delivering course content.', 'category' => 'Teaching Quality', 'question_type' => 'rating', 'display_order' => 1],
            ['question_text' => 'Lecturer attends classes punctually and utilizes allocated lecture time effectively.', 'category' => 'Punctuality', 'question_type' => 'rating', 'display_order' => 2],
            ['question_text' => 'Course materials, slides, and references were comprehensive and shared in a timely manner.', 'category' => 'Course Materials', 'question_type' => 'rating', 'display_order' => 3],
            ['question_text' => 'Lecturer encourages interactive participation and responds effectively to student inquiries.', 'category' => 'Engagement', 'question_type' => 'rating', 'display_order' => 4],
            ['question_text' => 'Overall rating for this lecturer and course.', 'category' => 'Overall Performance', 'question_type' => 'rating', 'display_order' => 5],
        ];

        foreach ($defaultQuestions as $q) {
            $survey->questions()->create($q);
        }

        return redirect()->route('admin.evaluation-surveys.index')->with('success', 'Evaluation survey created with default questions!');
    }

    public function show(EvaluationSurvey $survey)
    {
        $survey->load(['questions', 'responses.course', 'responses.lecturer', 'responses.student']);
        
        return view('admin.evaluation_surveys.show', compact('survey'));
    }
}
