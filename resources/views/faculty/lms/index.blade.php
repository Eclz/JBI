@extends('layouts.app')

@section('title', 'Faculty LMS')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">LMS Analytics</h2>
            <p class="text-muted mb-0">Track student progress across your courses.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course</th>
                            <th>Semester</th>
                            <th class="text-end">Students</th>
                            <th class="text-end">Avg Progress</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $entry)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $entry['course']->name }}</div>
                                    <small class="text-muted">{{ $entry['course']->code ?? $entry['course']->course_code }}</small>
                                </td>
                                <td>{{ $entry['course']->semester->name ?? 'N/A' }}</td>
                                <td class="text-end">{{ $entry['students_count'] }}</td>
                                <td class="text-end">{{ $entry['average_progress'] }}%</td>
                                <td class="text-end"><a href="{{ route('faculty.lms.show', $entry['course']) }}" class="btn btn-sm btn-primary">View Learners</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No courses assigned.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
