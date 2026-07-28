@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <h3 class="mb-0" style="color: #ffffff; font-weight: 600;">
                        <i class="bi bi-file-earmark-text me-2"></i>My Assignments
                    </h3>
                    <p class="mb-0 mt-2" style="color: rgba(255, 255, 255, 0.9);">
                        View and submit your course assignments
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Total</h6>
                            <h3 class="mb-0 fw-bold">{{ $assignments->count() }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e3f2fd;">
                            <i class="bi bi-file-earmark-text fs-4" style="color: #2196f3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Pending</h6>
                            <h3 class="mb-0 fw-bold" style="color: #ff9800;">
                                {{ $assignments->where('submissions', function($submissions) { return $submissions->isEmpty(); })->count() }}
                            </h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #fff3e0;">
                            <i class="bi bi-clock-history fs-4" style="color: #ff9800;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Submitted</h6>
                            <h3 class="mb-0 fw-bold" style="color: #4caf50;">
                                @php
                                    $submittedCount = 0;
                                    foreach($assignments as $a) {
                                        $sub = $a->submissions->first();
                                        if($sub && $sub->status != 'draft') $submittedCount++;
                                    }
                                @endphp
                                {{ $submittedCount }}
                            </h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e8f5e9;">
                            <i class="bi bi-check-circle fs-4" style="color: #4caf50;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Overdue</h6>
                            <h3 class="mb-0 fw-bold" style="color: #f44336;">
                                @php
                                    $overdueCount = 0;
                                    foreach($assignments as $a) {
                                        if($a->is_overdue && !$a->submissions->first()) $overdueCount++;
                                    }
                                @endphp
                                {{ $overdueCount }}
                            </h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #ffebee;">
                            <i class="bi bi-exclamation-triangle fs-4" style="color: #f44336;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="px-4 py-3" style="color: #495057; font-weight: 600;">Assignment</th>
                                    <th class="py-3" style="color: #495057; font-weight: 600;">Course</th>
                                    <th class="py-3" style="color: #495057; font-weight: 600;">Type</th>
                                    <th class="py-3" style="color: #495057; font-weight: 600;">Due Date</th>
                                    <th class="py-3" style="color: #495057; font-weight: 600;">Points</th>
                                    <th class="py-3" style="color: #495057; font-weight: 600;">Status</th>
                                    <th class="py-3 text-end pe-4" style="color: #495057; font-weight: 600;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                    @php
                                        $submission = $assignment->submissions->first();
                                        $isOverdue = $assignment->is_overdue;
                                        $isSubmitted = $submission && $submission->status != 'draft';
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="fw-semibold" style="color: #212529;">{{ $assignment->title }}</div>
                                                    @if($assignment->description)
                                                        <div class="small text-muted mt-1">{{ Str::limit($assignment->description, 60) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge" style="background-color: #e3f2fd; color: #1976d2;">
                                                {{ $assignment->course->code }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-capitalize" style="color: #495057;">{{ $assignment->type }}</span>
                                        </td>
                                        <td class="py-3">
                                            <div style="color: {{ $isOverdue && !$isSubmitted ? '#f44336' : '#495057' }};">
                                                <i class="bi bi-calendar3 me-1"></i>{{ $assignment->due_date->format('M d, Y') }}
                                            </div>
                                            <div class="small text-muted">{{ $assignment->due_date->format('h:i A') }}</div>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-semibold" style="color: #495057;">{{ $assignment->max_points }}</span>
                                        </td>
                                        <td class="py-3">
                                            @if($isSubmitted)
                                                @if($submission->status == 'graded')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Graded
                                                    </span>
                                                @else
                                                    <span class="badge bg-info text-white">
                                                        <i class="bi bi-clock me-1"></i>Submitted
                                                    </span>
                                                @endif
                                            @elseif($isOverdue)
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-end pe-4">
                                            <a href="{{ route('student.assignments.show', $assignment) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">No assignments found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($assignments->hasPages())
                <div class="mt-4">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
