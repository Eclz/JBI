<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->with(['course', 'questions'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('faculty.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('faculty.quizzes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'max_attempts' => 'required|integer|min:1',
            'show_results' => 'required|in:immediately,after_deadline,manual',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $course = Course::findOrFail($request->course_id);

        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $quiz = Quiz::create(array_merge(
            $request->all(),
            [
                'created_by' => Auth::id(),
                'is_published' => $request->boolean('is_published'),
            ]
        ));

        return redirect()->route('faculty.quizzes.questions', $quiz)
            ->with('success', 'Quiz created. Now add questions.');
    }

    public function show(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->load(['course', 'questions', 'attempts.student']);

        $statistics = [
            'total_attempts' => $quiz->attempts()->count(),
            'completed' => $quiz->attempts()->where('status', 'completed')->count(),
            'average_score' => $quiz->attempts()->where('status', 'completed')->avg('score'),
        ];

        return view('faculty.quizzes.show', compact('quiz', 'statistics'));
    }

    public function edit(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $courses = Course::where('instructor_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('faculty.quizzes.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'max_attempts' => 'required|integer|min:1',
            'show_results' => 'required|in:immediately,after_deadline,manual',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
        ]);

        $quiz->update($request->all());

        return redirect()->route('faculty.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->delete();

        return redirect()->route('faculty.quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    public function questions(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->load('questions');

        return view('faculty.quizzes.questions', compact('quiz'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'points' => 'required|numeric|min:0',
            'options' => 'nullable|string',
            'correct_answer' => 'required',
            'explanation' => 'nullable|string',
        ]);

        $options = null;
        if ($request->question_type === 'multiple_choice') {
            $optionsInput = preg_split('/\r\n|\r|\n/', (string) $request->input('options', ''));

            $options = array_values(array_filter(array_map('trim', (array) $optionsInput), 'strlen'));
            if (count($options) < 2) {
                return back()
                    ->withErrors(['options' => 'Please provide at least two options.'])
                    ->withInput();
            }
        }

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => $request->question_text,
            'question_type' => $request->question_type,
            'points' => $request->points,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
            'explanation' => $request->explanation,
            'order' => $quiz->questions()->max('order') + 1,
        ]);

        return back()->with('success', 'Question added successfully.');
    }

    public function updateQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $this->authorizeQuiz($quiz);

        $request->validate([
            'question_text' => 'required|string',
            'points' => 'required|numeric|min:0',
            'options' => 'nullable|string',
            'correct_answer' => 'required',
            'explanation' => 'nullable|string',
        ]);

        $options = null;
        if ($question->question_type === 'multiple_choice') {
            $optionsInput = preg_split('/\r\n|\r|\n/', (string) $request->input('options', ''));

            $options = array_values(array_filter(array_map('trim', (array) $optionsInput), 'strlen'));
            if (count($options) < 2) {
                return back()
                    ->withErrors(['options' => 'Please provide at least two options.'])
                    ->withInput();
            }
        }

        $question->update([
            'question' => $request->question_text,
            'points' => $request->points,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
            'explanation' => $request->explanation,
        ]);

        return back()->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(Quiz $quiz, QuizQuestion $question)
    {
        $this->authorizeQuiz($quiz);

        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    public function attempts(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $attempts = $quiz->attempts()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('faculty.quizzes.attempts', compact('quiz', 'attempts'));
    }

    private function authorizeQuiz(Quiz $quiz)
    {
        if ($quiz->course->instructor_id !== Auth::id()) {
            abort(403);
        }
    }
}
