@extends('layouts.app')

@section('title', $course->name . ' - Course Details')

@section('content')
{{-- Added proper container padding and improved styling --}}
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            {{-- Updated header with better styling --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-dark fw-bold">{{ $course->name }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">My Courses</a></li>
                            <li class="breadcrumb-item active">{{ $course->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('student.courses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Courses
                    </a>
                </div>
            </div>

            {{-- Enhanced tab styling with better visibility and hover effects --}}
            <ul class="nav nav-tabs mb-4 border-bottom-0" id="courseTab" role="tablist" style="background: white; border-radius: 8px; padding: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" style="color: #4F46E5; border: none; font-weight: 500; padding: 12px 20px;">
                        <i class="bi bi-info-circle me-2" style="font-size: 18px;"></i> Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab" style="color: #6B7280; border: none; font-weight: 500; padding: 12px 20px;">
                        <i class="bi bi-file-earmark-text me-2" style="font-size: 18px;"></i> Assignments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab" style="color: #6B7280; border: none; font-weight: 500; padding: 12px 20px;">
                        <i class="bi bi-clipboard-check me-2" style="font-size: 18px;"></i> Exams
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="quizzes-tab" data-bs-toggle="tab" data-bs-target="#quizzes" type="button" role="tab" style="color: #6B7280; border: none; font-weight: 500; padding: 12px 20px;">
                        <i class="bi bi-question-circle me-2" style="font-size: 18px;"></i> Quizzes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button" role="tab" style="color: #6B7280; border: none; font-weight: 500; padding: 12px 20px;">
                        <i class="bi bi-award me-2" style="font-size: 18px;"></i> Grades
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" style="color: #6B7280; border: none; font-weight: 500; padding: 12px 20px;">
                        <i class="bi bi-calendar-check me-2" style="font-size: 18px;"></i> Attendance
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees" type="button" role="tab" style="color: #6B7280; border: none; font-weight: 500; padding: 12px 20px;">
                        <i class="bi bi-cash-coin me-2" style="font-size: 18px;"></i> Fees
                    </button>
                </li>
            </ul>

            {{-- Added custom CSS for better tab interactions --}}
            <style>
                .nav-tabs .nav-link {
                    transition: all 0.3s ease;
                }
                .nav-tabs .nav-link:hover {
                    background-color: #F3F4F6;
                    border-radius: 6px;
                }
                .nav-tabs .nav-link.active {
                    color: #4F46E5 !important;
                    background-color: #EEF2FF;
                    border-radius: 6px;
                }
                .nav-tabs .nav-link.active i {
                    color: #4F46E5;
                }
            </style>

            <div class="tab-content" id="courseTabContent">
                {{-- Overview Tab --}}
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-1 text-dark fw-bold">{{ $course->name }}</h5>
                                    <p class="text-muted mb-2">{{ $course->code }} • {{ $course->credits }} Credits</p>
                                    <p class="mb-3">{{ $course->description }}</p>

                                    <div class="row">
                                        <div class="col-sm-6 mb-2">
                                            <strong class="text-dark">Instructor:</strong>
                                            <span class="text-muted">{{ $course->instructor->name ?? 'Not Assigned' }}</span>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <strong class="text-dark">Department:</strong>
                                            <span class="text-muted">{{ $course->department->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong class="text-dark">Semester:</strong>
                                            <span class="text-muted">{{ $course->semester->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong class="text-dark">Enrolled:</strong>
                                            <span class="text-muted">{{ $enrollment->enrollment_date->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border-end py-2">
                                                <h4 class="mb-0 text-primary fw-bold">{{ number_format($overallGrade ?? 0, 1) }}%</h4>
                                                <small class="text-muted">Overall Grade</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-end py-2">
                                                <h4 class="mb-0 text-success fw-bold">{{ number_format($attendanceRate ?? 0, 1) }}%</h4>
                                                <small class="text-muted">Attendance</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="py-2">
                                                <h4 class="mb-0 text-info fw-bold">{{ $course->assignments->count() }}</h4>
                                                <small class="text-muted">Assignments</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Existing code for Quick Stats, Recent Materials, Course Actions --}}
                    <div class="row">
                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Progress -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Progress</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 d-flex justify-content-between">
                                        <span class="text-muted">Coursework (40%)</span>
                                        <span class="fw-bold">{{ number_format($progress['coursework'] ?? 0, 1) }}%</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between">
                                        <span class="text-muted">Final Exam (60%)</span>
                                        <span class="fw-bold">{{ number_format($progress['exam'] ?? 0, 1) }}%</span>
                                    </div>
                                    <div class="mb-2 d-flex justify-content-between">
                                        <span class="text-muted">Overall</span>
                                        <span class="fw-bold text-primary">{{ number_format($progress['overall'] ?? 0, 1) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                             style="width: {{ $progress['overall'] ?? 0 }}%"
                                             aria-valuenow="{{ $progress['overall'] ?? 0 }}"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Quick Stats</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                {{-- Added null coalescing operators for safety --}}
                                                <h5 class="mb-0">{{ $attendedClasses ?? 0 }}/{{ $totalClasses ?? 0 }}</h5>
                                                <small class="text-muted">Classes Attended</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            {{-- Added null coalescing operators for safety --}}
                                            <h5 class="mb-0">{{ $earnedPoints ?? 0 }}/{{ $totalPoints ?? 0 }}</h5>
                                            <small class="text-muted">Points Earned</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Materials -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">Recent Materials</h6>
                                    <a href="{{ route('student.courses.materials', $course) }}" class="btn btn-sm btn-outline-primary">
                                        View All
                                    </a>
                                </div>
                                <div class="card-body p-0">
                                    {{-- Added safety check for recentMaterials variable --}}
                                    @if(isset($recentMaterials) && $recentMaterials->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentMaterials as $material)
                                                <div class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            @switch($material->type)
                                                                @case('document')
                                                                    <i class="bi bi-file-text text-primary"></i>
                                                                    @break
                                                                @case('video')
                                                                    <i class="bi bi-play-circle text-danger"></i>
                                                                    @break
                                                                @case('audio')
                                                                    <i class="bi bi-music-note text-info"></i>
                                                                    @break
                                                                @case('image')
                                                                    <i class="bi bi-image text-success"></i>
                                                                    @break
                                                                @case('link')
                                                                    <i class="bi bi-link-45deg text-warning"></i>
                                                                    @break
                                                                @default
                                                                    <i class="bi bi-file text-secondary"></i>
                                                            @endswitch
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-medium">{{ $material->title }}</div>
                                                            <small class="text-muted">{{ $material->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="bi bi-folder-x text-muted"></i>
                                            <p class="text-muted mb-0 mt-2">No materials available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Course Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Course Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('student.courses.materials', $course) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-folder"></i> View Materials
                                        </a>
                                        <a href="{{ route('student.courses.attendance', $course) }}" class="btn btn-outline-info">
                                            <i class="bi bi-calendar-check"></i> View Attendance
                                        </a>
                                        <a href="{{ route('student.courses.grades', $course) }}" class="btn btn-outline-success">
                                            <i class="bi bi-graph-up"></i> View Grades
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assignments Tab --}}
                <div class="tab-pane fade" id="assignments" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-file-earmark-text text-primary me-2"></i>Course Assignments
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @if($course->assignments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-dark">Assignment</th>
                                                <th class="text-dark">Due Date</th>
                                                <th class="text-dark">Points</th>
                                                <th class="text-dark">Grade</th>
                                                <th class="text-dark">Status</th>
                                                <th class="text-dark">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($course->assignments as $assignment)
                                                @php
                                                    $grade = $grades->get($assignment->id);
                                                    $isOverdue = $assignment->due_date->isPast();
                                                    $hasSubmission = $assignment->submissions()->where('user_id', auth()->id())->exists();
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <div class="fw-medium text-dark">{{ $assignment->title }}</div>
                                                            @if($assignment->description)
                                                                <small class="text-muted">{{ Str::limit($assignment->description, 50) }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="{{ $isOverdue ? 'text-danger' : 'text-muted' }}">
                                                            {{ $assignment->due_date->format('M d, Y') }}
                                                            <br>
                                                            <small>{{ $assignment->due_date->format('g:i A') }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="text-dark">{{ $assignment->max_points }}</td>
                                                    <td>
                                                        @if($grade)
                                                            <span class="fw-medium text-dark">{{ $grade->points_earned }}/{{ $assignment->max_points }}</span>
                                                            <br>
                                                            <small class="text-success">{{ number_format(($grade->points_earned / $assignment->max_points) * 100, 1) }}%</small>
                                                        @else
                                                            <span class="text-muted">Not graded</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($grade)
                                                            <span class="badge bg-success">Graded</span>
                                                        @elseif($hasSubmission)
                                                            <span class="badge bg-info">Submitted</span>
                                                        @elseif($isOverdue)
                                                            <span class="badge bg-danger">Overdue</span>
                                                        @else
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-clipboard-x display-1 text-muted"></i>
                                    <h5 class="mt-3 text-dark">No Assignments</h5>
                                    <p class="text-muted">No assignments have been posted for this course yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Added Exams Tab --}}
                <div class="tab-pane fade" id="exams" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-clipboard-check text-primary me-2"></i>Course Exams
                            </h5>
                            <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-outline-primary">
                                View All Exams
                            </a>
                        </div>
                        <div class="card-body">
                            @php
                                $courseExams = \App\Models\Exam::where('course_id', $course->id)
                                    ->where('is_published', true)
                                    ->orderBy('start_time', 'desc')
                                    ->get();
                            @endphp

                            @if($courseExams->count() > 0)
                                <div class="row">
                                    @foreach($courseExams as $exam)
                                        @php
                                            $attempt = $exam->studentAttempt(auth()->id());
                                        @endphp
                                        <div class="col-md-6 mb-3">
                                            <div class="card border {{ $exam->isActive() ? 'border-success' : '' }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-0 text-dark fw-bold">{{ $exam->title }}</h6>
                                                        @if($exam->isActive())
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($exam->isUpcoming())
                                                            <span class="badge bg-warning">Upcoming</span>
                                                        @else
                                                            <span class="badge bg-secondary">Ended</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-muted small mb-2">{{ $exam->description }}</p>
                                                    <div class="small text-muted mb-3">
                                                        <div><i class="bi bi-clock me-1"></i> Duration: {{ $exam->duration_minutes }} minutes</div>
                                                        <div><i class="bi bi-calendar me-1"></i> Start: {{ $exam->start_time->format('M d, Y g:i A') }}</div>
                                                        <div><i class="bi bi-calendar-x me-1"></i> End: {{ $exam->end_time->format('M d, Y g:i A') }}</div>
                                                        @if($exam->required_payment > 0)
                                                            <div><i class="bi bi-cash me-1"></i> Fee: {{ $currencyCode }} {{ number_format($exam->required_payment, 2) }}</div>
                                                        @endif
                                                    </div>

                                                    @if($attempt)
                                                        <div class="mb-2">
                                                            @if($attempt->status === 'graded')
                                                                <div class="alert alert-success py-2 mb-0">
                                                                    <strong>Grade:</strong> {{ $attempt->marks_obtained }}/{{ $exam->total_marks }}
                                                                    ({{ number_format($attempt->percentage, 1) }}%)
                                                                </div>
                                                            @elseif($attempt->status === 'submitted')
                                                                <div class="alert alert-info py-2 mb-0">
                                                                    Submitted - Awaiting grading
                                                                </div>
                                                            @elseif($attempt->status === 'in_progress')
                                                                <div class="alert alert-warning py-2 mb-0">
                                                                    In Progress
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-sm btn-primary w-100">
                                                        <i class="bi bi-eye me-1"></i> View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-clipboard-x display-1 text-muted"></i>
                                    <h5 class="mt-3 text-dark">No Exams</h5>
                                    <p class="text-muted">No exams have been scheduled for this course yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Added Quizzes Tab Content --}}
                <div class="tab-pane fade" id="quizzes" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark fw-bold">Course Quizzes</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $quizzes = $course->quizzes()
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                            @endphp

                            @if($quizzes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Quiz Title</th>
                                                <th>Duration</th>
                                                <th>Total Points</th>
                                                <th>Attempts</th>
                                                <th>Best Score</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($quizzes as $quiz)
                                                @php
                                                    $attempts = $quiz->attempts()->where('user_id', auth()->id())->count();
                                                    $bestAttempt = $quiz->bestAttempt(auth()->id());
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong class="text-dark">{{ $quiz->title }}</strong>
                                                        @if($quiz->start_time && $quiz->end_time)
                                                            <br><small class="text-muted">Available: {{ $quiz->start_time->format('M d, Y H:i') }} - {{ $quiz->end_time->format('M d, Y H:i') }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $quiz->duration }} mins</td>
                                                    <td>{{ $quiz->questions->sum('points') }}</td>
                                                    <td>
                                                        @if($quiz->max_attempts)
                                                            {{ $attempts }}/{{ $quiz->max_attempts }}
                                                        @else
                                                            {{ $attempts }}/Unlimited
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($bestAttempt)
                                                            <span class="badge bg-success">{{ number_format($bestAttempt->percentage, 1) }}%</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($quiz->canAttempt(auth()->id()))
                                                            <span class="badge bg-success">Available</span>
                                                        @elseif($attempts >= $quiz->max_attempts && $quiz->max_attempts)
                                                            <span class="badge bg-danger">Max Attempts Reached</span>
                                                        @else
                                                            <span class="badge bg-secondary">Not Available</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-eye me-1"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-question-circle" style="font-size: 64px; color: #CBD5E1;"></i>
                                    <p class="text-muted mt-3">No quizzes available yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Added Grades Tab --}}
                <div class="tab-pane fade" id="grades" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-award text-primary me-2"></i>Your Grades
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ number_format($overallGrade ?? 0, 1) }}%</h3>
                                            <small>Overall Grade</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $earnedPoints ?? 0 }}</h3>
                                            <small>Points Earned</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $totalPoints ?? 0 }}</h3>
                                            <small>Total Points</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $grades->count() }}</h3>
                                            <small>Graded Items</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($grades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-dark">Assignment</th>
                                                <th class="text-dark">Score</th>
                                                <th class="text-dark">Percentage</th>
                                                <th class="text-dark">Feedback</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($grades as $grade)
                                                <tr>
                                                    <td class="text-dark fw-medium">{{ $grade->assignment->title ?? 'N/A' }}</td>
                                                    <td class="text-dark">{{ $grade->points_earned }}/{{ $grade->assignment->max_points ?? 0 }}</td>
                                                    <td>
                                                        @php
                                                            $percentage = $grade->assignment ? ($grade->points_earned / $grade->assignment->max_points) * 100 : 0;
                                                        @endphp
                                                        <span class="badge {{ $percentage >= 70 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                                            {{ number_format($percentage, 1) }}%
                                                        </span>
                                                    </td>
                                                    <td class="text-muted">{{ $grade->feedback ?? 'No feedback' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-award display-1 text-muted"></i>
                                    <h5 class="mt-3 text-dark">No Grades Yet</h5>
                                    <p class="text-muted">Your assignments haven't been graded yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Added Attendance Tab --}}
                <div class="tab-pane fade" id="attendance" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-calendar-check text-primary me-2"></i>Attendance Record
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $attendedClasses ?? 0 }}</h3>
                                            <small>Classes Attended</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $totalClasses ?? 0 }}</h3>
                                            <small>Total Classes</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ number_format($attendanceRate ?? 0, 1) }}%</h3>
                                            <small>Attendance Rate</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $attendanceRecords = \App\Models\Attendance::where('course_id', $course->id)
                                    ->where('user_id', auth()->id())
                                    ->orderBy('attendance_date', 'desc')
                                    ->limit(20)
                                    ->get();
                            @endphp

                            @if($attendanceRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-dark">Date</th>
                                                <th class="text-dark">Status</th>
                                                <th class="text-dark">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attendanceRecords as $record)
                                                <tr>
                                                    <td class="text-dark">{{ $record->attendance_date->format('M d, Y') }}</td>
                                                    <td>
                                                        @if($record->status === 'present')
                                                            <span class="badge bg-success">Present</span>
                                                        @elseif($record->status === 'absent')
                                                            <span class="badge bg-danger">Absent</span>
                                                        @elseif($record->status === 'late')
                                                            <span class="badge bg-warning">Late</span>
                                                        @else
                                                            <span class="badge bg-info">Excused</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted">{{ $record->remarks ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                                    <h5 class="mt-3 text-dark">No Attendance Records</h5>
                                    <p class="text-muted">No attendance has been recorded yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Added Fees Tab --}}
                <div class="tab-pane fade" id="fees" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="bi bi-cash-coin text-primary me-2"></i>Course Fees
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $courseFee = \App\Models\CourseFee::where('course_id', $course->id)
                                    ->where('user_id', auth()->id())
                                    ->first();
                            @endphp

                            @if($courseFee)
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h4 class="mb-0">{{ $currencyCode }} {{ number_format($courseFee->amount, 2) }}</h4>
                                                <small>Total Fee</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h4 class="mb-0">{{ $currencyCode }} {{ number_format($courseFee->paid_amount, 2) }}</h4>
                                                <small>Paid</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-danger text-white">
                                            <div class="card-body text-center">
                                                <h4 class="mb-0">{{ $currencyCode }} {{ number_format($courseFee->amount - $courseFee->paid_amount, 2) }}</h4>
                                                <small>Balance</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h4 class="mb-0">{{ $courseFee->status }}</h4>
                                                <small>Status</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($courseFee->amount - $courseFee->paid_amount > 0)
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        You have an outstanding balance of {{ $currencyCode }} {{ number_format($courseFee->amount - $courseFee->paid_amount, 2) }}.
                                        Please make a payment to avoid any holds on your account.
                                    </div>
                                    <a href="{{ route('student.fees.index') }}" class="btn btn-primary">
                                        <i class="bi bi-credit-card me-1"></i> Make Payment
                                    </a>
                                @else
                                    <div class="alert alert-success">
                                        <i class="bi bi-check-circle me-2"></i>
                                        All fees for this course have been paid. Thank you!
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-cash display-1 text-muted"></i>
                                    <h5 class="mt-3 text-dark">No Fee Information</h5>
                                    <p class="text-muted">Fee details for this course are not available yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link:hover {
    color: #0d6efd;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.card-title {
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
    }
}
</style>
@endpush

{{-- Added JavaScript to ensure tabs work properly --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tabs
        var triggerTabList = [].slice.call(document.querySelectorAll('#courseTab button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    })
</script>
@endpush
