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
        if ($request->input('academic_year_id') === '') {
            $request->merge(['academic_year_id' => null]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_number' => 'required|integer|in:1,2',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
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
        $survey->load(['questions', 'responses.course', 'responses.lecturer', 'responses.student', 'academicYear']);
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        
        $totalResponsesCount = $survey->responses->count();

        // Calculate statistics per question
        $questionStats = [];
        $overallTotalSum = 0;
        $overallTotalCount = 0;

        foreach ($survey->questions as $question) {
            $ratings = [];
            $starCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

            foreach ($survey->responses as $resp) {
                $ans = $resp->answers[$question->id] ?? null;
                if ($ans !== null && is_numeric($ans)) {
                    $val = (int)$ans;
                    $ratings[] = $val;
                    if (isset($starCounts[$val])) {
                        $starCounts[$val]++;
                    }
                    $overallTotalSum += $val;
                    $overallTotalCount++;
                }
            }

            $count = count($ratings);
            $avg = $count > 0 ? array_sum($ratings) / $count : 0;

            $questionStats[$question->id] = [
                'avg' => round($avg, 2),
                'count' => $count,
                'starCounts' => $starCounts,
            ];
        }

        $overallAverage = $overallTotalCount > 0 ? round($overallTotalSum / $overallTotalCount, 2) : 0;

        return view('admin.evaluation_surveys.show', compact('survey', 'questionStats', 'totalResponsesCount', 'overallAverage', 'academicYears'));
    }

    public function update(Request $request, EvaluationSurvey $survey)
    {
        if ($request->input('academic_year_id') === '') {
            $request->merge(['academic_year_id' => null]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_number' => 'required|integer|in:1,2',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $survey->update($validated);

        return redirect()->route('admin.evaluation-surveys.show', $survey)->with('success', 'Evaluation survey updated successfully!');
    }

    public function destroy(EvaluationSurvey $survey)
    {
        $survey->delete();

        return redirect()->route('admin.evaluation-surveys.index')->with('success', 'Evaluation survey deleted successfully!');
    }

    public function toggleStatus(EvaluationSurvey $survey)
    {
        $survey->update(['is_active' => !$survey->is_active]);

        $statusStr = $survey->is_active ? 'activated' : 'closed';
        return redirect()->back()->with('success', "Evaluation survey {$statusStr} successfully!");
    }

    public function addQuestion(Request $request, EvaluationSurvey $survey)
    {
        $validated = $request->validate([
            'question_text' => 'required|string|max:500',
            'category' => 'required|string|max:100',
            'question_type' => 'required|in:rating,text,boolean',
        ]);

        $maxOrder = $survey->questions()->max('display_order') ?? 0;
        $validated['display_order'] = $maxOrder + 1;

        $survey->questions()->create($validated);

        return redirect()->route('admin.evaluation-surveys.show', $survey)->with('success', 'New evaluation question added successfully!');
    }

    public function destroyQuestion(EvaluationSurvey $survey, EvaluationQuestion $question)
    {
        if ($question->survey_id === $survey->id) {
            $question->delete();
        }

        return redirect()->route('admin.evaluation-surveys.show', $survey)->with('success', 'Question deleted successfully!');
    }
}

