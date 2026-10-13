@extends('layouts.app')

@section('title', $course->code . ' Assignments')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('faculty.courses.show', $course) }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Back to course
            </a>
            <h1 class="h3 mt-2 mb-0">{{ $course->code }} Assignments</h1>
            <p class="text-muted mb-0">{{ $course->name }}</p>
        </div>
        <a href="{{ route('faculty.assignments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>New Assignment
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Due</th>
                        <th>Max Score</th>
                        <th>Submissions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="fw-semibold">{{ $assignment->title }}</td>
                            <td>{{ optional($assignment->due_date)->format('M d, Y g:i A') ?? '—' }}</td>
                            <td>{{ $assignment->max_score ?? $assignment->total_marks ?? '—' }}</td>
                            <td>{{ $assignment->submissions->count() }}</td>
                            <td class="text-end">
                                <a href="{{ route('faculty.assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('faculty.assignments.submissions', $assignment) }}" class="btn btn-sm btn-outline-secondary">Submissions</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No assignments have been created for this course yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
