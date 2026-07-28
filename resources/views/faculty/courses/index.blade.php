@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-book me-2"></i>My Courses
            </h1>
            <p class="text-muted mb-0">Courses you are teaching this semester</p>
        </div>
    </div>

    <!-- Course Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-book stats-icon"></i>
                    <h3 class="mt-2">{{ $courses->total() }}</h3>
                    <p class="mb-0">Total Courses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-people stats-icon"></i>
                    <h3 class="mt-2">{{ $courses->sum(function($course) { return $course->enrollments->count(); }) }}</h3>
                    <p class="mb-0">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-check stats-icon"></i>
                    <h3 class="mt-2">{{ $courses->sum(function($course) { return $course->assignments->count(); }) }}</h3>
                    <p class="mb-0">Assignments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark stats-icon"></i>
                    <h3 class="mt-2">{{ $courses->sum(function($course) { return $course->materials->count(); }) }}</h3>
                    <p class="mb-0">Materials</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="row">
        @forelse($courses as $course)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 text-primary fw-bold">{{ $course->code }}</h6>
                            <small class="text-muted">{{ $course->department->name }}</small>
                        </div>
                        <span class="badge bg-{{ $course->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">{{ $course->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($course->description, 100) }}</p>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Credits</small>
                                <div class="fw-bold">{{ $course->credits }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Enrolled</small>
                                <div class="fw-bold">{{ $course->enrollments->count() }}/{{ $course->capacity }}</div>
                            </div>
                        </div>

                        @if($course->schedule_days && $course->schedule_time)
                            <div class="mb-3">
                                <small class="text-muted">Schedule</small>
                                <div>{{ $course->schedule_days }} {{ $course->schedule_time }}</div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <small class="text-muted">Room</small>
                            <div>{{ $course->room ?? 'TBA' }}</div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $course->semester->name ?? 'No Semester' }}</small>
                            <a href="{{ route('faculty.courses.show', $course) }}" class="btn btn-sm btn-primary">
                                Manage Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-book display-1 text-muted"></i>
                    <h4 class="mt-3">No courses assigned</h4>
                    <p class="text-muted">You don't have any courses assigned for this semester.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($courses->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $courses->links() }}
    </div>
@endif
@endsection
