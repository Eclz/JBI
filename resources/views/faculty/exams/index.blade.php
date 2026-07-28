@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Exam Management</h1>
            <p class="text-muted mb-0">Create and manage examinations for your courses</p>
        </div>
        <a href="{{ route('faculty.exams.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create New Exam
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Exam Title</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Course</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Type</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Date & Time</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Mode</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Total Marks</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                        <tr>
                            <td class="align-middle" style="padding: 1rem;">
                                <div class="fw-semibold" style="color: #1e293b;">{{ $exam->title }}</div>
                                <small class="text-muted">{{ Str::limit($exam->description, 50) }}</small>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <div style="color: #1e293b;">{{ $exam->course->code }}</div>
                                <small class="text-muted">{{ $exam->course->name }}</small>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <span class="badge bg-primary">{{ ucfirst($exam->exam_type) }}</span>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <div style="color: #1e293b;">{{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $exam->start_time }} - {{ $exam->end_time }}</small>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                @if($exam->exam_mode === 'online')
                                    <span class="badge bg-success">Online</span>
                                @elseif($exam->exam_mode === 'offline')
                                    <span class="badge bg-secondary">Offline</span>
                                @else
                                    <span class="badge bg-info">Hybrid</span>
                                @endif
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <span style="color: #1e293b; font-weight: 600;">{{ $exam->total_marks }}</span>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('faculty.exams.show', $exam) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('faculty.exams.edit', $exam) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('faculty.exams.attempts', $exam) }}" class="btn btn-sm btn-outline-info" title="View Attempts">
                                        <i class="bi bi-people"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No exams created yet. Create your first exam to get started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($exams->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $exams->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
