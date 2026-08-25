@extends('layouts.app')

@section('title', 'Academic Setup')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-diagram-3 me-2 text-primary"></i>Academic Setup</h1>
            <p class="text-muted mb-0">Complete the university structure in this order. Each step feeds the next.</p>
        </div>
        @if($academicYear)
            <span class="badge bg-primary fs-6">{{ $academicYear->name }}</span>
        @endif
    </div>

    <div class="alert alert-info border-0 shadow-sm">
        <strong>Programmes are qualifications</strong> offered by JBI, such as a Certificate in Theology.
        <strong>Courses are the individual modules</strong> taught inside a programme and should be added only when the approved curriculum supplies module codes, credits and semesters.
    </div>

    <div class="row g-3">
        @php
            $steps = [
                [1, 'Programme Levels', 'Define Certificate through Doctorate.', 'admin.program-levels.index', 'layers', $counts['levels']],
                [2, 'Faculties / Schools', 'Create the eight official academic schools.', 'admin.faculties.index', 'building', $counts['faculties']],
                [3, 'Departments', 'Place each academic department under a school.', 'admin.departments.index', 'diagram-2', $counts['departments']],
                [4, 'Programmes', 'Add each qualification at its correct level.', 'admin.programs.index', 'journal-bookmark', $counts['programs']],
                [5, 'Fee Structures', 'Assign local ZAR and international USD fees.', 'admin.fees.structures.index', 'cash-stack', $counts['fees']],
                [6, 'Courses / Modules', 'Add curriculum modules, credits and semesters.', 'admin.courses.index', 'book', $counts['courses']],
            ];
        @endphp
        @foreach($steps as [$number, $title, $description, $route, $icon, $count])
            <div class="col-lg-4 col-md-6">
                <a href="{{ route($route) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm academic-step-card">
                        <div class="card-body d-flex gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;">{{ $number }}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-dark mb-1"><i class="bi bi-{{ $icon }} me-1 text-primary"></i>{{ $title }}</h5>
                                    <span class="badge bg-light text-primary">{{ $count }}</span>
                                </div>
                                <p class="text-muted small mb-0">{{ $description }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h5><i class="bi bi-shield-check text-success me-2"></i>Imported source</h5>
            <p class="mb-1">Official JBI University academic catalogue and tuition schedule for 2026/2027.</p>
            <small class="text-muted">Records imported from the official website are updated by stable codes, so running the synchronisation again will not create duplicates.</small>
        </div>
    </div>
</div>
@endsection
