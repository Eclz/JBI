@extends('layouts.app')

@section('title', 'Faculty Timetable & Schedules')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="mb-1 fw-bold text-dark">
                <i class="bi bi-calendar3 text-primary me-2"></i>Timetable & Teaching Schedules
            </h2>
            <p class="text-muted mb-0">
                View your personal teaching schedule or explore full institutional timetables across all programmes
            </p>
        </div>
        <div class="d-flex gap-2">
            <!-- Scope Switcher: My Schedule vs All University -->
            <a href="{{ route('faculty.timetables.index', ['scope' => 'my', 'type' => $type]) }}" 
               class="btn {{ $scope === 'my' ? 'btn-primary fw-bold shadow-sm' : 'btn-outline-secondary' }}">
                <i class="bi bi-person-badge me-1"></i>My Teaching Schedule
            </a>
            <a href="{{ route('faculty.timetables.index', ['scope' => 'all', 'type' => $type]) }}" 
               class="btn {{ $scope === 'all' ? 'btn-primary fw-bold shadow-sm' : 'btn-outline-secondary' }}">
                <i class="bi bi-globe me-1"></i>All University Timetables
            </a>
        </div>
    </div>

    <!-- Type Tabs: Teaching, Tests, Exams -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link px-4 py-2 {{ $type === 'teaching' ? 'active fw-bold' : 'text-dark' }}" 
                       href="{{ route('faculty.timetables.index', ['scope' => $scope, 'type' => 'teaching', 'program_id' => $programId, 'year_of_study' => $yearOfStudy]) }}">
                        <i class="bi bi-journal-text me-2"></i>Teaching Timetable
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-2 {{ $type === 'tests' ? 'active fw-bold' : 'text-dark' }}" 
                       href="{{ route('faculty.timetables.index', ['scope' => $scope, 'type' => 'tests', 'program_id' => $programId, 'year_of_study' => $yearOfStudy]) }}">
                        <i class="bi bi-patch-check me-2"></i>Tests Timetable
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-2 {{ $type === 'exams' ? 'active fw-bold' : 'text-dark' }}" 
                       href="{{ route('faculty.timetables.index', ['scope' => $scope, 'type' => 'exams', 'program_id' => $programId, 'year_of_study' => $yearOfStudy]) }}">
                        <i class="bi bi-pencil-square me-2"></i>Exams Timetable
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 px-3 py-1 bg-light rounded text-muted small">
                <i class="bi bi-info-circle text-primary"></i>
                <span>Showing <strong>{{ count($slots) }}</strong> scheduled {{ $type }} slot(s)</span>
            </div>
        </div>
    </div>

    @if($scope === 'all')
    <!-- Filters for All University Timetables -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('faculty.timetables.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="scope" value="all">
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="col-md-4">
                    <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Filter by Programme (All) --</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ $programId == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="year_of_study" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Filter by Year of Study --</option>
                        <option value="1" {{ $yearOfStudy == '1' ? 'selected' : '' }}>Year 1</option>
                        <option value="2" {{ $yearOfStudy == '2' ? 'selected' : '' }}>Year 2</option>
                        <option value="3" {{ $yearOfStudy == '3' ? 'selected' : '' }}>Year 3</option>
                        <option value="4" {{ $yearOfStudy == '4' ? 'selected' : '' }}>Year 4</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="day_of_week" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- All Days --</option>
                        @foreach($days as $d)
                            <option value="{{ $d }}" {{ $dayOfWeek == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="{{ route('faculty.timetables.index', ['scope' => 'all', 'type' => $type]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Timetable Slots Grid -->
    @if(count($slots) > 0)
        <div class="row g-4">
            @foreach($days as $day)
                @php
                    $daySlots = $slots->filter(fn($s) => strtolower($s->day_of_week) === strtolower($day));
                @endphp
                @if($daySlots->count() > 0 || $scope === 'my')
                <div class="col-lg-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; overflow: hidden;">
                        <!-- Day Header -->
                        <div class="card-header py-3 d-flex justify-content-between align-items-center" 
                             style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: white;">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-calendar-event me-2"></i>{{ $day }}
                            </h6>
                            <span class="badge bg-white text-primary fw-bold">{{ $daySlots->count() }} Class{{ $daySlots->count() === 1 ? '' : 'es' }}</span>
                        </div>

                        <div class="card-body p-3 bg-light">
                            @forelse($daySlots as $slot)
                                <div class="card border-0 shadow-sm mb-3 bg-white" style="border-radius: 10px; border-left: 4px solid #2563eb !important;">
                                    <div class="card-body p-3">
                                        <!-- Time & Venue -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary-subtle text-primary border border-primary fw-bold px-2 py-1">
                                                <i class="bi bi-clock me-1"></i>{{ $slot->start_time }} - {{ $slot->end_time }}
                                            </span>
                                            <span class="badge bg-secondary-subtle text-dark border">
                                                <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $slot->room_venue }}
                                            </span>
                                        </div>

                                        <!-- Course Info -->
                                        <h6 class="fw-bold text-dark mb-1">
                                            {{ $slot->course->code ?? $slot->course->course_code ?? 'Course' }}: {{ $slot->course->name ?? 'N/A' }}
                                        </h6>

                                        <!-- Programme & Year -->
                                        <div class="text-muted small mb-2">
                                            <i class="bi bi-mortarboard me-1 text-primary"></i>
                                            {{ $slot->program->name ?? 'General' }} &bull; Year {{ $slot->year_of_study }} (Sem {{ $slot->semester_number }})
                                        </div>

                                        @if($slot->notes)
                                            <div class="p-2 mb-2 bg-light rounded small text-muted">
                                                <i class="bi bi-info-circle me-1"></i>{{ $slot->notes }}
                                            </div>
                                        @endif

                                        @if($scope === 'all' && $slot->faculty)
                                            <div class="small text-muted mb-2">
                                                <i class="bi bi-person me-1"></i>Lecturer: <strong>{{ $slot->faculty->full_name }}</strong>
                                            </div>
                                        @endif

                                        <!-- Quick Take Attendance Button -->
                                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                            @if($slot->course_id)
                                                <a href="{{ route('faculty.courses.attendance.show', [$slot->course_id, 'take' => 1, 'date' => date('Y-m-d'), 'start_time' => $slot->start_time, 'end_time' => $slot->end_time, 'notes' => 'Timetable Lesson: ' . $slot->day_of_week . ' ' . $slot->start_time . ' (' . $slot->room_venue . ')']) }}" 
                                                   class="btn btn-sm btn-outline-success fw-bold w-100">
                                                    <i class="bi bi-check2-square me-1"></i>Take Attendance
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-cup-hot" style="font-size: 1.8rem; color: #94a3b8;"></i>
                                    <p class="small mb-0 mt-2">No scheduled lessons for {{ $day }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <i class="bi bi-calendar-x" style="font-size: 3.5rem; color: #cbd5e1;"></i>
                <h5 class="fw-bold text-dark mt-3">No Timetable Slots Found</h5>
                <p class="text-muted mb-3">
                    @if($scope === 'my')
                        You have no classes scheduled under your teaching profile for this semester.
                    @else
                        No timetable slots match the selected filters.
                    @endif
                </p>
                @if($scope === 'my')
                    <a href="{{ route('faculty.timetables.index', ['scope' => 'all']) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-globe me-1"></i>Browse All University Timetables
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
