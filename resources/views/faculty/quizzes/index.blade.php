@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Quiz Management</h1>
            <p class="text-muted mb-0">Create and manage quizzes for your courses</p>
        </div>
        <a href="{{ route('faculty.quizzes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create New Quiz
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
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Quiz Title</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Course</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Questions</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Duration</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Total Marks</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Status</th>
                            <th style="color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quizzes as $quiz)
                        <tr>
                            <td class="align-middle" style="padding: 1rem;">
                                <div class="fw-semibold" style="color: #1e293b;">{{ $quiz->title }}</div>
                                <small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <div style="color: #1e293b;">{{ $quiz->course->code }}</div>
                                <small class="text-muted">{{ $quiz->course->name }}</small>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <span class="badge bg-info">{{ $quiz->questions->count() }} Questions</span>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <span style="color: #1e293b;">{{ $quiz->duration_minutes }} min</span>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <span style="color: #1e293b; font-weight: 600;">{{ $quiz->total_marks }}</span>
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                @if(now()->lt($quiz->start_time))
                                    <span class="badge bg-secondary">Upcoming</span>
                                @elseif(now()->between($quiz->start_time, $quiz->end_time))
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Ended</span>
                                @endif
                            </td>
                            <td class="align-middle" style="padding: 1rem;">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('faculty.quizzes.show', $quiz) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('faculty.quizzes.questions', $quiz) }}" class="btn btn-sm btn-outline-success" title="Manage Questions">
                                        <i class="bi bi-list-task"></i>
                                    </a>
                                    <a href="{{ route('faculty.quizzes.edit', $quiz) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No quizzes created yet. Create your first quiz to get started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($quizzes->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $quizzes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
