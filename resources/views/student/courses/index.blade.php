@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-book me-2"></i>My Courses
        </h1>
        <p class="text-muted mb-0">Your enrolled courses for this semester</p>
    </div>
</div>

<!-- Course Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-book stats-icon"></i>
                <h3 class="mt-2">{{ $enrollments->total() }}</h3>
                <p class="mb-0">Enrolled Courses</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-award stats-icon"></i>
                <h3 class="mt-2">{{ $enrollments->sum(function($enrollment) { return $enrollment->course->credits; }) }}</h3>
                <p class="mb-0">Total Credits</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-check stats-icon"></i>
                <h3 class="mt-2">{{ $enrollments->sum(function($enrollment) { return $enrollment->course->assignments->count(); }) }}</h3>
                <p class="mb-0">Total Assignments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-percent stats-icon"></i>
                <h3 class="mt-2">85%</h3>
                <p class="mb-0">Avg Attendance</p>
            </div>
        </div>
    </div>
</div>

<!-- Courses Grid -->
<div class="row">
    @forelse($enrollments as $enrollment)
        @php $course = $enrollment->course; @endphp
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-primary fw-bold">{{ $course->code }}</h6>
                        <small class="text-muted">{{ $course->department->name }}</small>
                    </div>
                    <span class="badge bg-success">Enrolled</span>
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
                            <small class="text-muted">Instructor</small>
                            <div class="fw-bold">{{ $course->instructor->first_name ?? 'TBA' }} {{ $course->instructor->last_name ?? '' }}</div>
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
                    
                    <div class="mb-3">
                        <small class="text-muted">Enrolled</small>
                        <div>{{ $enrollment->enrollment_date->format('M d, Y') }}</div>
                    </div>
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">{{ $course->semester->name ?? 'No Semester' }}</small>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary">
                                View Course
                            </a>
                            <a href="{{ route('student.courses.materials', $course) }}" class="btn btn-outline-primary">
                                Materials
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-book display-1 text-muted"></i>
                <h4 class="mt-3">No courses enrolled</h4>
                <p class="text-muted">You are not enrolled in any courses for this semester.</p>
                <p class="text-muted">Please contact the administration office for course enrollment.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($enrollments->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $enrollments->links() }}
    </div>
@endif
@endsection
