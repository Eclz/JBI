<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $announcements = Announcement::with(['creator', 'course'])
            ->when(!$user->hasPermission('announcements', 'view'), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where(function ($live) use ($user) {
                        $live->active()->forRole($user->getAssignedRoleIdentifiers());
                    })->orWhere('created_by', $user->id);
                });
            })
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->priority))
            ->when($request->filled('course'), fn ($q) => $q->where('course_id', $request->course))
            ->when($request->filled('target'), fn ($q) => $q->whereJsonContains('target_roles', $request->target))
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $hasFacultyRole = $user->hasRole(['faculty', 'lecturer']);
        $hasStudentRole = $user->hasRole('student');
        $courses = Course::query()
            ->when(!$user->hasPermission('courses', 'view'), function ($query) use ($user, $hasFacultyRole, $hasStudentRole) {
                $query->where(function ($scope) use ($user, $hasFacultyRole, $hasStudentRole) {
                    if ($hasFacultyRole) {
                        $scope->where('instructor_id', $user->id);
                    }
                    if ($hasStudentRole) {
                        $method = $hasFacultyRole ? 'orWhereHas' : 'whereHas';
                        $scope->{$method}('enrollments', fn ($enrollments) => $enrollments->where('user_id', $user->id));
                    }
                    if (!$hasFacultyRole && !$hasStudentRole) {
                        $scope->whereRaw('1 = 0');
                    }
                });
            })
            ->orderBy('code')
            ->get();

        return view('announcements.index', compact('announcements', 'courses'));
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        $announcement->load(['creator', 'course', 'department']);

        return view('announcements.show', compact('announcement'));
    }

    public function create()
    {
        $this->authorize('create', Announcement::class);

        return view('announcements.create', $this->formData());
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $this->authorize('create', Announcement::class);

        $announcement = Announcement::create($this->payload($request) + ['created_by' => Auth::id()]);

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        return view('announcements.edit', ['announcement' => $announcement] + $this->formData());
    }

    public function update(StoreAnnouncementRequest $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $data = $this->payload($request);
        // Keep the original publish date when an already-published item is edited.
        if ($data['is_published'] && $announcement->published_at && !$request->filled('published_at')) {
            $data['published_at'] = $announcement->published_at;
        }

        $announcement->update($data);

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Whitelisted attributes shared by store() and update().
     */
    private function payload(StoreAnnouncementRequest $request): array
    {
        $published = $request->boolean('is_published');

        return [
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $request->input('type'),
            'priority' => $request->input('priority'),
            'course_id' => $request->input('course_id'),
            'department_id' => $request->input('department_id'),
            'target_roles' => $request->input('target_roles'),
            'is_published' => $published,
            'send_email' => $request->boolean('send_email'),
            'send_sms' => $request->boolean('send_sms'),
            'published_at' => $published ? ($request->input('published_at') ?: now()) : null,
            'expires_at' => $request->input('expires_at'),
        ];
    }

    private function formData(): array
    {
        $user = Auth::user();

        return [
            'courses' => Course::query()
                ->when($user->hasRole('faculty'), fn ($q) => $q->where('instructor_id', $user->id))
                ->where('status', 'active')
                ->orderBy('code')
                ->get(),
            'departmentOptions' => Department::where('is_active', true)->orderBy('name')->get(),
            'roleOptions' => ['student' => 'Students', 'faculty' => 'Faculty', 'admin' => 'Administrators', 'parent' => 'Parents'],
        ];
    }
}
