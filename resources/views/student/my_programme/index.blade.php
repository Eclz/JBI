@extends('layouts.app')

@section('title', 'My Programme Courses')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-journal-bookmark text-primary me-2"></i>MY PROGRAMME COURSES
            </h5>
            <p class="text-muted small mb-0">{{ $program?->name ?? 'BACHELOR OF SCIENCE IN SOFTWARE ENGINEERING (BSSE)' }}</p>
        </div>
        <a href="{{ route('student.enrollment.index') }}" class="btn btn-primary fw-bold">
            <i class="bi bi-person-plus-fill me-1"></i>ENROLL FOR NEXT SEMESTER
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @for($year = 1; $year <= 4; $year++)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom border-2 border-primary">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-mortarboard me-2"></i>YEAR {{ $year }} COURSES
                </h6>
            </div>
            <div class="card-body p-0">
                @for($sem = 1; $sem <= 2; $sem++)
                    <div class="bg-light px-4 py-2 border-bottom border-top fw-bold text-dark small text-uppercase">
                        SEMESTER {{ $sem == 1 ? 'I' : 'II' }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-white text-muted">
                                <tr>
                                    <th style="width: 120px;">CODE</th>
                                    <th>COURSE TITLE</th>
                                    <th style="width: 100px;">CREDITS</th>
                                    <th>PREREQUISITE</th>
                                    <th style="width: 150px;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $semCourses = $groupedCourses[$year][$sem] ?? [];
                                @endphp
                                @forelse($semCourses as $course)
                                    @php
                                        $enrolledStatus = $enrolledCourseIds[$course->id] ?? null;
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $course->code }}</td>
                                        <td class="fw-semibold text-dark">{{ $course->title }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $course->credits ?? 4 }} CU</span></td>
                                        <td><span class="text-muted">{{ $course->prerequisite ?? 'None' }}</span></td>
                                        <td>
                                            @if($enrolledStatus === 'enrolled' || $enrolledStatus === 'completed' || $enrolledStatus === 'passed')
                                                <span class="badge bg-primary px-2 py-1">ENROLLED / PASSED</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">AVAILABLE</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted text-center py-3">No course records listed for Year {{ $year }} Semester {{ $sem }}.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endfor
            </div>
        </div>
    @endfor
</div>
@endsection
