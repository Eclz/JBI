<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['author', 'course'])
            ->when(Auth::user()->role === 'student', function ($query) {
                $query->where(function ($q) {
                    $q->where('target_role', 'all')
                      ->orWhere('target_role', 'student')
                      ->orWhereHas('course.enrollments', function ($sq) {
                          $sq->where('student_id', Auth::id());
                      });
                });
            })
            ->when(Auth::user()->role === 'faculty', function ($query) {
                $query->where(function ($q) {
                    $q->where('target_role', 'all')
                      ->orWhere('target_role', 'faculty')
                      ->orWhere('author_id', Auth::id());
                });
            })
            ->when(request('priority'), function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when(request('course'), function ($query, $course) {
                $query->where('course_id', $course);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $courses = Course::when(Auth::user()->role === 'faculty', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->when(Auth::user()->role === 'student', function ($query) {
                $query->whereHas('enrollments', function ($q) {
                    $q->where('student_id', Auth::id());
                });
            })
            ->get();

        return view('announcements.index', compact('announcements', 'courses'));
    }

    public function show(Announcement $announcement)
    {
        $announcement->load(['author', 'course']);
        
        // Mark as read for current user
        $announcement->markAsReadBy(Auth::user());
        
        return view('announcements.show', compact('announcement'));
    }

    public function create()
    {
        $this->authorize('create', Announcement::class);
        
        $courses = Course::where('instructor_id', Auth::id())
            ->where('status', 'active')
            ->get();
        
        return view('announcements.create', compact('courses'));
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $this->authorize('create', Announcement::class);
        
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
            'target_role' => $request->target_role,
            'course_id' => $request->course_id,
            'author_id' => Auth::id(),
            'attachment_path' => $attachmentPath,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]);

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        
        $courses = Course::where('instructor_id', Auth::id())
            ->where('status', 'active')
            ->get();
        
        return view('announcements.edit', compact('announcement', 'courses'));
    }

    public function update(StoreAnnouncementRequest $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        
        $attachmentPath = $announcement->attachment_path;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
            'target_role' => $request->target_role,
            'course_id' => $request->course_id,
            'attachment_path' => $attachmentPath,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') && !$announcement->published_at ? now() : $announcement->published_at,
        ]);

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);
        
        if ($announcement->attachment_path) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }
        
        $announcement->delete();
        
        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
