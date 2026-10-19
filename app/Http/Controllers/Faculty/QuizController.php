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
        $validated = $request->validate([
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

        $course = Course::findOrFail($validated['course_id']);

        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['status'] = $request->input('status', $validated['is_published'] ? 'published' : 'draft');
        if ($validated['status'] === 'active') {
            $validated['is_published'] = true;
        }
        $validated['created_by'] = Auth::id();

        $quiz = Quiz::create($validated);

        return redirect()->route('faculty.quizzes.questions', $quiz)
            ->with('success', 'Quiz created. Now add questions.');
    }

    public function show(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->load(['course', 'questions', 'attempts.student', 'attempts.user']);

        $completedAttempts = $quiz->attempts()->whereIn('status', ['submitted', 'graded', 'completed']);

        $statistics = [
            'total_attempts' => $quiz->attempts()->count(),
            'completed' => (clone $completedAttempts)->count(),
            'average_score' => round((clone $completedAttempts)->avg('percentage') ?? 0, 1),
            'highest_score' => (clone $completedAttempts)->max('percentage') ?? 0,
            'lowest_score' => (clone $completedAttempts)->min('percentage') ?? 0,
            'pass_count' => (clone $completedAttempts)->where('score', '>=', $quiz->passing_marks)->count(),
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

        $validated = $request->validate([
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
            'is_published' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published,active,closed',
        ]);

        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($request->filled('status')) {
            $validated['status'] = $request->status;
            if ($validated['status'] === 'closed') {
                if ($quiz->end_time && $quiz->end_time->isFuture()) {
                    $validated['end_time'] = now();
                }
            } elseif ($validated['status'] === 'active') {
                $validated['is_published'] = true;
            } elseif ($validated['status'] === 'draft') {
                $validated['is_published'] = false;
            }
        }

        $quiz->update($validated);

        return redirect()->route('faculty.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully.');
    }

    public function close(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->close();

        return back()->with('success', "Quiz '{$quiz->title}' has been closed. Students can no longer make new attempts.");
    }

    public function reopen(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $days = (int) $request->input('days', 7);
        if ($days <= 0) {
            $days = 7;
        }

        $quiz->reopen($days);

        return back()->with('success', "Quiz '{$quiz->title}' has been opened/activated for students until " . $quiz->end_time->format('M d, Y H:i') . '.');
    }

    public function updateStatus(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $request->validate([
            'status' => 'required|in:draft,published,active,closed',
        ]);

        $status = $request->input('status');
        if ($status === 'closed') {
            $quiz->close();
        } elseif ($status === 'active') {
            $quiz->reopen((int) $request->input('days', 7));
        } else {
            $quiz->status = $status;
            $quiz->is_published = ($status !== 'draft');
            $quiz->save();
        }

        return back()->with('success', "Quiz status updated to " . ucfirst($status) . ".");
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
