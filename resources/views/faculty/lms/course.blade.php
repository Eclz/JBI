@extends('layouts.app')

@section('title', 'Course Learner Progress')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $course->name }} - Learner Progress</h2>
            <p class="text-muted mb-0">{{ $course->semester->name ?? 'No Semester' }}</p>
        </div>
        <a href="{{ route('faculty.lms.index') }}" class="btn btn-outline-secondary">Back to LMS Analytics</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th class="text-end">Notes Read</th>
                            <th class="text-end">Videos Watched</th>
                            <th class="text-end">Completed</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Progress</th>
                            <th>Bar</th>
                            <th class="text-end">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $row)
                            @php
                                $student = $row['student'];
                                $progress = $row['progress'];
                                $materialStats = $row['material_stats'];
                            @endphp
                            <tr>
                                <td>{{ $student->full_name ?: ($student->name ?? 'N/A') }}</td>
                                <td class="text-end">{{ $materialStats['notes_read'] }}/{{ $materialStats['notes_total'] }}</td>
                                <td class="text-end">{{ $materialStats['videos_watched'] }}/{{ $materialStats['videos_total'] }}</td>
                                <td class="text-end">{{ $progress['completed'] }}</td>
                                <td class="text-end">{{ $progress['total'] }}</td>
                                <td class="text-end">{{ $progress['percent'] }}%</td>
                                <td style="min-width: 220px;">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar" style="width: {{ $progress['percent'] }}%"></div>
                                    </div>
                                </td>
                                <td class="text-end">{{ $row['last_activity'] ? \Carbon\Carbon::parse($row['last_activity'])->diffForHumans() : 'No activity' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No enrolled students.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
