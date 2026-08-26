<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'all');
        $replyTo = $request->query('reply_to');
        $replySubject = $request->query('subject');

        if ($type === 'sent') {
            $query = Message::with(['sender', 'receiver'])
                ->where('sender_id', $user->id);
        } else {
            $query = Message::with(['sender', 'receiver'])
                ->where(function($q) use ($user) {
                    $q->where('receiver_id', $user->id)
                      ->orWhereNull('receiver_id'); // System broadcast alerts
                });

            if ($type === 'personal') {
                $query->where('type', 'message');
            } elseif ($type === 'alerts') {
                $query->whereIn('type', ['assignment_alert', 'quiz_alert', 'exam_alert', 'system']);
            }
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = Message::where(function($q) use ($user) {
            $q->where('receiver_id', $user->id)->orWhereNull('receiver_id');
        })->where('is_read', false)->count();

        // Get recipients (students and faculty)
        $recipients = User::where('id', '!=', $user->id)
            ->whereIn('role', ['student', 'faculty', 'admin'])
            ->select('id', 'first_name', 'last_name', 'email', 'role')
            ->get();

        $groupCourses = collect();
        if ($user->isAdmin()) {
            $groupCourses = \App\Models\Course::orderBy('course_code')->get();
        } elseif ($user->isFaculty()) {
            $groupCourses = \App\Models\Course::where('instructor_id', $user->id)->orderBy('course_code')->get();
        }

        return view('messages.index', compact('messages', 'unreadCount', 'recipients', 'type', 'replyTo', 'replySubject', 'groupCourses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'type' => 'message',
            'is_read' => false,
        ]);

        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'year' => 'nullable|integer|min:1|max:4',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $user = Auth::user();
        $course = \App\Models\Course::findOrFail($request->course_id);

        if (!$user->isAdmin() && $course->instructor_id !== $user->id) {
            abort(403, 'You are not authorized to send messages to this course group.');
        }

        $query = User::role('student')
            ->whereHas('courseEnrollments', function ($q) use ($course) {
                $q->where('course_id', $course->id)->where('status', 'enrolled');
            });

        if ($request->filled('year')) {
            $query->whereHas('studentProfile', function ($q) use ($request) {
                $q->where('year_of_study', $request->year);
            });
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return redirect()->back()->withErrors(['group_error' => 'No enrolled students found matching the criteria.'])->withInput();
        }

        foreach ($students as $student) {
            Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $student->id,
                'subject' => $request->subject,
                'body' => $request->body,
                'type' => 'message',
                'is_read' => false,
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($student->email)
                    ->queue(new \App\Mail\CourseGroupMail($student, $user, $course, $request->subject, $request->body));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed sending course group email to {$student->email}: " . $e->getMessage());
            }
        }

        $yearText = $request->filled('year') ? " (Year {$request->year})" : "";
        return redirect()->route('messages.index')->with('success', "Group message sent successfully to " . $students->count() . " students enrolled in {$course->course_code}{$yearText}.");
    }

    public function show(Message $message)
    {
        $user = Auth::user();
        if ($message->receiver_id && $message->receiver_id !== $user->id && $message->sender_id !== $user->id) {
            abort(403);
        }

        if ($message->receiver_id === $user->id && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('messages.show', compact('message'));
    }
}
