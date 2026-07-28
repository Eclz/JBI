@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Faculty Dashboard</h1>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 16 16">
                                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1" style="font-size: 0.875rem;">Total Courses</h6>
                            <h3 class="mb-0" style="font-weight: 700; color: #1e293b;">{{ $totalCourses }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 16 16">
                                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1" style="font-size: 0.875rem;">Total Students</h6>
                            <h3 class="mb-0" style="font-weight: 700; color: #1e293b;">{{ $totalStudents }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#8b5e34" viewBox="0 0 16 16">
                                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1" style="font-size: 0.875rem;">Pending Grading</h6>
                            <h3 class="mb-0" style="font-weight: 700; color: #1e293b;">{{ $pendingAssignments }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0891b2" viewBox="0 0 16 16">
                                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1" style="font-size: 0.875rem;">Today's Attendance</h6>
                            <h3 class="mb-0" style="font-weight: 700; color: #1e293b;">{{ $todayAttendance }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3" style="color: #1e293b; font-weight: 600;">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('faculty.assignments.create') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-file-earmark-plus me-2"></i>Create Assignment
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('faculty.exams.create') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-clipboard-check me-2"></i>Create Exam
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('faculty.quizzes.create') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-question-circle me-2"></i>Create Quiz
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('faculty.courses.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-book me-2"></i>My Courses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- My Courses -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">My Courses</h5>
                    <a href="{{ route('faculty.courses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($courses as $course)
                        <div class="p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('faculty.courses.show', $course) }}" class="text-decoration-none" style="color: #1e293b;">
                                            {{ $course->code }} - {{ $course->name }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-0">{{ $course->semester->name ?? 'N/A' }} | {{ $course->enrolled_students_count }} students</p>
                                </div>
                                <span class="badge bg-primary">{{ $course->credits }} credits</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <p class="mb-0">No courses assigned yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">Recent Submissions</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($recentSubmissions as $submission)
                        <div class="p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-size: 0.9rem;">{{ $submission->assignment->title }}</h6>
                                    <p class="text-muted small mb-0">
                                        {{ $submission->student->name }} |
                                        {{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : 'Not submitted' }}
                                    </p>
                                </div>
                                <a href="{{ route('faculty.assignments.submissions', $submission->assignment) }}" class="btn btn-sm btn-outline-primary">Grade</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <p class="mb-0">No recent submissions</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
