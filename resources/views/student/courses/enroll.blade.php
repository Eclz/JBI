@extends('layouts.app')

@section('title', 'Course Enrollment')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-journal-plus me-2"></i>Course Enrollment
            </h1>
            <p class="text-muted mb-0">Enroll in courses for the current semester</p>
        </div>
        <a href="{{ route('student.courses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to My Courses
        </a>
    </div>

    @if(!$currentSemester)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No active semester is available for enrollment. Please contact the administration office.
        </div>
    @else
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between">
                    <div>
                        <h5 class="mb-1">{{ $currentSemester->name }}</h5>
                        <div class="text-muted">Semester dates: {{ $currentSemester->start_date?->format('M d, Y') ?? 'TBA' }} - {{ $currentSemester->end_date?->format('M d, Y') ?? 'TBA' }}</div>
                        <div class="text-muted">
                            Registration window:
                            @if($currentSemester->registration_start && $currentSemester->registration_end)
                                {{ $currentSemester->registration_start->format('M d, Y') }} - {{ $currentSemester->registration_end->format('M d, Y') }}
                            @else
                                Not set
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 mt-md-0">
                        @if($registrationOpen)
                            <span class="badge bg-success">Registration Open</span>
                        @else
                            <span class="badge bg-danger">Registration Closed</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!$profile)
        <div class="alert alert-danger">
            <i class="bi bi-x-circle me-2"></i>
            Your student profile is incomplete. Please contact the administration office to update your program details.
        </div>
    @elseif($profile->year_of_study === null)
        <div class="alert alert-warning">
            <i class="bi bi-info-circle me-2"></i>
            Your year of study is not set. Enrollment will be limited until this is updated.
        </div>
    @endif

    <div class="row">
        @forelse($availableCourses as $course)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 text-primary fw-bold">{{ $course->code }}</h6>
                            <small class="text-muted">{{ $course->department->name ?? 'No Department' }}</small>
                        </div>
                        @if($course->is_full)
                            <span class="badge bg-danger">Full</span>
                        @else
                            <span class="badge bg-info">Open</span>
                        @endif
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

                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Program</small>
                                <div class="fw-bold">{{ $course->program->name ?? ($course->department->name ?? 'Department') }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Year of Study</small>
                                <div class="fw-bold">{{ $course->year_of_study ?? 'All years' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <form action="{{ route('student.courses.enroll', $course) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100" {{ $course->is_full || !$registrationOpen ? 'disabled' : '' }}>
                                <i class="bi bi-plus-circle me-1"></i> Enroll
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-journal-x display-1 text-muted"></i>
                    <h4 class="mt-3">No courses available</h4>
                    <p class="text-muted">Eligible courses will appear here when registration is open.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
