@extends('layouts.app')

@section('title', $course->name . ' - Course Details')

@section('content')
<div class="container-fluid">
    <!-- Course Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="h3 mb-2">{{ $course->name }}</h1>
                            <p class="text-muted mb-2">{{ $course->course_code }}</p>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-primary">{{ $course->credits }} Credits</span>
                                <span class="badge bg-info">{{ $course->semester->name ?? 'N/A' }}</span>
                                <span class="badge bg-success">{{ ucfirst($enrollment->status) }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <p class="mb-1"><strong>Instructor:</strong> {{ $course->instructor->name ?? 'TBA' }}</p>
                            <p class="mb-1"><strong>Department:</strong> {{ $course->department->name ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Room:</strong> {{ $course->room ?? 'TBA' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" id="courseTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                        <i class="fas fa-home me-2"></i>Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab">
                        <i class="fas fa-book me-2"></i>Materials
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab">
                        <i class="fas fa-tasks me-2"></i>Assignments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button" role="tab">
                        <i class="fas fa-chart-line me-2"></i>Grades
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">
                        <i class="fas fa-calendar-check me-2"></i>Attendance
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="courseTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Course Description</h5>
                            <p class="card-text">{{ $course->description ?? 'No description available.' }}</p>

                            @if($course->learning_objectives)
                                <h6 class="mt-4">Learning Objectives</h6>
                                <p>{{ $course->learning_objectives }}</p>
                            @endif

                            @if($course->assessment_methods)
                                <h6 class="mt-4">Assessment Methods</h6>
                                <p>{{ $course->assessment_methods }}</p>
                            @endif

                            <!-- Recent Announcements -->
                            <h6 class="mt-4">Recent Announcements</h6>
                            @if($course->announcements->count() > 0)
                                @foreach($course->announcements->take(3) as $announcement)
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">{{ $announcement->title }}</h6>
                                        <p class="mb-0">{{ Str::limit($announcement->content, 150) }}</p>
                                        <small class="text-muted">{{ $announcement->created_at->format('M d, Y') }}</small>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No announcements yet.</p>
                            @endif

                            <!-- Upcoming Assignments -->
                            <h6 class="mt-4">Upcoming Assignments</h6>
                            @php
                                $upcomingAssignments = $course->assignments()
                                    ->where('due_date', '>', now())
                                    ->where('is_published', true)
                                    ->orderBy('due_date', 'asc')
                                    ->take(3)
                                    ->get();
                            @endphp

                            @if($upcomingAssignments->count() > 0)
                                <div class="list-group">
                                    @foreach($upcomingAssignments as $assignment)
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $assignment->title }}</h6>
                                                <small class="text-danger">Due: {{ $assignment->due_date->format('M d, Y') }}</small>
                                            </div>
                                            <p class="mb-1">{{ Str::limit($assignment->description, 100) }}</p>
                                            <small>{{ $assignment->max_points }} points</small>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No upcoming assignments.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Materials Tab -->
                <div class="tab-pane fade" id="materials" role="tabpanel">
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Course Materials</h5>
                                <a href="{{ route('student.courses.materials', $course) }}" class="btn btn-outline-primary btn-sm">
                                    View All Materials
                                </a>
                            </div>

                            @if($course->materials->where('is_published', true)->count() > 0)
                                <div class="row">
                                    @foreach($course->materials->where('is_published', true)->take(6) as $material)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        @switch($material->type)
                                                            @case('document')
                                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                                @break
                                                            @case('video')
                                                                <i class="fas fa-video text-primary me-2"></i>
                                                                @break
                                                            @case('audio')
                                                                <i class="fas fa-volume-up text-success me-2"></i>
                                                                @break
                                                            @case('image')
                                                                <i class="fas fa-image text-warning me-2"></i>
                                                                @break
                                                            @case('link')
                                                                <i class="fas fa-external-link-alt text-info me-2"></i>
                                                                @break
                                                            @default
                                                                <i class="fas fa-file text-secondary me-2"></i>
                                                        @endswitch
                                                        <h6 class="card-title mb-0">{{ $material->title }}</h6>
                                                    </div>
                                                    @if($material->description)
                                                        <p class="card-text small">{{ Str::limit($material->description, 80) }}</p>
                                                    @endif
                                                    <div class="mt-auto">
                                                        @if($material->type === 'link')
                                                            <a href="{{ $material->external_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-external-link-alt me-1"></i>Open Link
                                                            </a>
                                                        @else
                                                            <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-download me-1"></i>Download
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No materials available yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Assignments Tab -->
                <div class="tab-pane fade" id="assignments" role="tabpanel">
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Assignments</h5>

                            @if($course->assignments->where('is_published', true)->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Assignment</th>
                                                <th>Due Date</th>
                                                <th>Points</th>
                                                <th>Status</th>
                                                <th>Grade</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($course->assignments->where('is_published', true) as $assignment)
                                                @php
                                                    $submission = $assignment->submissions()->where('user_id', Auth::id())->first();
                                                    $grade = $grades->where('assignment_id', $assignment->id)->first();
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $assignment->title }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ Str::limit($assignment->description, 50) }}</small>
                                                    </td>
                                                    <td>
                                                        {{ $assignment->due_date->format('M d, Y H:i') }}
                                                        @if($assignment->due_date->isPast())
                                                            <br><small class="text-danger">Overdue</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $assignment->max_points }}</td>
                                                    <td>
                                                        @if($submission)
                                                            @if($submission->status === 'submitted')
                                                                <span class="badge bg-success">Submitted</span>
                                                            @elseif($submission->status === 'graded')
                                                                <span class="badge bg-primary">Graded</span>
                                                            @else
                                                                <span class="badge bg-warning">{{ ucfirst($submission->status) }}</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">Not Submitted</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($grade)
                                                            <strong>{{ $grade->points_earned }}/{{ $assignment->max_points }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $grade->letter_grade ?? '' }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('student.assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No assignments available yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Grades Tab -->
                <div class="tab-pane fade" id="grades" role="tabpanel">
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Grade Summary</h5>

                            @if($grades->count() > 0)
                                @php
                                    $totalPoints = $course->assignments->where('is_published', true)->sum('max_points');
                                    $earnedPoints = $grades->sum('points_earned');
                                    $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;

                                    // Calculate letter grade
                                    $letterGrade = 'F';
                                    if ($percentage >= 90) $letterGrade = 'A';
                                    elseif ($percentage >= 80) $letterGrade = 'B';
                                    elseif ($percentage >= 70) $letterGrade = 'C';
                                    elseif ($percentage >= 60) $letterGrade = 'D';
                                @endphp

                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h2 class="display-4 mb-0">{{ $letterGrade }}</h2>
                                            <p class="text-muted">Current Grade</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="mb-0">{{ $percentage }}%</h3>
                                            <p class="text-muted">Percentage</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="mb-0">{{ $earnedPoints }}</h3>
                                            <p class="text-muted">Points Earned</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="mb-0">{{ $totalPoints }}</h3>
                                            <p class="text-muted">Total Points</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="progress mb-4" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                        {{ $percentage }}%
                                    </div>
                                </div>

                                <h6>Individual Assignment Grades</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Assignment</th>
                                                <th>Points Earned</th>
                                                <th>Total Points</th>
                                                <th>Percentage</th>
                                                <th>Letter Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($grades as $grade)
                                                @php
                                                    $assignment = $course->assignments->find($grade->assignment_id);
                                                    $gradePercentage = $assignment && $assignment->max_points > 0
                                                        ? round(($grade->points_earned / $assignment->max_points) * 100, 2)
                                                        : 0;
                                                @endphp
                                                <tr>
                                                    <td>{{ $assignment->title ?? 'Unknown Assignment' }}</td>
                                                    <td>{{ $grade->points_earned }}</td>
                                                    <td>{{ $assignment->max_points ?? 0 }}</td>
                                                    <td>{{ $gradePercentage }}%</td>
                                                    <td>{{ $grade->letter_grade ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No grades available yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Attendance Tab -->
                <div class="tab-pane fade" id="attendance" role="tabpanel">
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Attendance Record</h5>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h2 class="display-4 mb-0 {{ $attendancePercentage >= 75 ? 'text-success' : 'text-danger' }}">
                                            {{ $attendancePercentage }}%
                                        </h2>
                                        <p class="text-muted">Attendance Rate</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h3 class="mb-0">{{ $attendanceRecords->where('status', 'present')->count() }}</h3>
                                        <p class="text-muted">Classes Attended</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h3 class="mb-0">
                                            {{-- {{ $course->attendance()->distinct('date')->count() }} --}}
                                        </h3>
                                        <p class="text-muted">Total Classes</p>
                                    </div>
                                </div>
                            </div>

                            <div class="progress mb-4" style="height: 20px;">
                                <div class="progress-bar {{ $attendancePercentage >= 75 ? 'bg-success' : 'bg-danger' }}"
                                     role="progressbar" style="width: {{ $attendancePercentage }}%">
                                    {{ $attendancePercentage }}%
                                </div>
                            </div>

                            @if($attendanceRecords->count() > 0)
                                <h6>Attendance Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attendanceRecords->sortByDesc('date') as $record)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                                    <td>
                                                        @if($record->status === 'present')
                                                            <span class="badge bg-success">Present</span>
                                                        @elseif($record->status === 'absent')
                                                            <span class="badge bg-danger">Absent</span>
                                                        @elseif($record->status === 'late')
                                                            <span class="badge bg-warning">Late</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $record->notes ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No attendance records available yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Bootstrap tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#courseTab button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)

        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>
@endpush
