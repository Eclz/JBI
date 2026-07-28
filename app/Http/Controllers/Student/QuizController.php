<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        $enrolledCourseIds = $student->courseEnrollments()
            ->where('status', 'enrolled')
            ->pluck('course_id');

        $availableQuizzes = Quiz::whereIn('course_id', $enrolledCourseIds)
            ->with('course', 'questions')
            ->orderBy('created_at', 'desc')
            ->get();

        $completedAttempts = QuizAttempt::where('user_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->with('quiz.course')
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('student.quizzes.index', compact('availableQuizzes', 'completedAttempts'));
    }

    public function show(Quiz $quiz)
    {
        $student = Auth::user();

        $enrollment = $student->courseEnrollments()
            ->where('course_id', $quiz->course_id)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'You must be enrolled in this course to view this quiz.');
        }

        $attempts = $quiz->studentAttempts($student->id);
        $bestAttempt = $quiz->bestAttempt($student->id);

        return view('student.quizzes.show', compact('quiz', 'attempts', 'bestAttempt'));
    }

    public function start(Quiz $quiz)
    {
        $student = Auth::user();

        if (!$quiz->canAttempt($student->id)) {
            return back()->with('error', 'You cannot attempt this quiz at this time.');
        }

        $attemptNumber = $quiz->attempts()
            ->where('user_id', $student->id)
            ->count() + 1;

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $student->id,
            'attempt_number' => $attemptNumber,
            'started_at' => Carbon::now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('student.quizzes.take', [$quiz, $attempt]);
    }

    public function take(Quiz $quiz, QuizAttempt $attempt)
    {
        $student = Auth::user();

        if ($attempt->user_id !== $student->id) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Invalid quiz attempt.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.show', $quiz)
                ->with('info', 'This quiz attempt has been completed.');
        }

        $questions = $quiz->questions;

        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.quizzes.take', compact('quiz', 'attempt', 'questions'));
    }

    public function submit(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        $student = Auth::user();

        if ($attempt->user_id !== $student->id || $attempt->status !== 'in_progress') {
            return back()->with('error', 'Invalid quiz submission.');
        }

        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $score = 0;
        $totalPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $studentAnswer = $validated['answers'][$question->id] ?? null;

            if ($question->checkAnswer($studentAnswer)) {
                $score += $question->points;
            }
        }

        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;

        $timeTaken = Carbon::parse($attempt->started_at)->diffInSeconds(Carbon::now());

        $attempt->update([
            'answers' => $validated['answers'],
            'submitted_at' => Carbon::now(),
            'time_taken_seconds' => $timeTaken,
            'score' => $score,
            'percentage' => $percentage,
            'status' => 'graded',
        ]);

        return redirect()->route('student.quizzes.result', [$quiz, $attempt])
            ->with('success', 'Quiz submitted successfully!');
    }

    public function result(Quiz $quiz, QuizAttempt $attempt)
    {
        $student = Auth::user();

        if ($attempt->user_id !== $student->id) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Invalid quiz attempt.');
        }

        $questions = $quiz->questions;

        return view('student.quizzes.result', compact('quiz', 'attempt', 'questions'));
    }
}
