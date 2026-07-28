@extends('layouts.app')

@section('title', 'Enrollment Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Enrollment Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- Fixed route from admin.dashboard to dashboard --}}
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.enrollments.index') }}">Enrollments</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Enrollment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Enrollment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Student</label>
                            <p>{{ $enrollment->student->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Student ID</label>
                            <p>{{ $enrollment->student->student_id ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Course</label>
                            <p>{{ $enrollment->course->course_code }} - {{ $enrollment->course->title }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Semester</label>
                            <p>{{ $enrollment->course->semester->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Enrollment Date</label>
                            <p>{{ $enrollment->enrollment_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <p>
                                <span class="badge {{ $enrollment->getStatusBadgeClass() }}">
                                    {{ $enrollment->getStatusText() }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($enrollment->completion_date)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Completion Date</label>
                            <p>{{ $enrollment->completion_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($enrollment->notes)
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label fw-bold">Notes</label>
                            <p>{{ $enrollment->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Grades -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Grades</h5>
                </div>
                <div class="card-body">
                    @if($enrollment->grades && $enrollment->grades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Assessment</th>
                                    <th>Grade</th>
                                    <th>Weight</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollment->grades as $grade)
                                <tr>
                                    <td>{{ $grade->assessment_name ?? 'N/A' }}</td>
                                    <td>{{ $grade->grade }}</td>
                                    <td>{{ $grade->weight ?? 'N/A' }}%</td>
                                    <td>{{ $grade->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">No grades recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Final Grade -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Final Grade</h5>
                </div>
                <div class="card-body">
                    @if($enrollment->final_grade)
                    <div class="text-center mb-3">
                        <h2 class="display-4">{{ $enrollment->final_grade }}%</h2>
                        @if($enrollment->letter_grade)
                        <h3 class="text-muted">{{ $enrollment->letter_grade }}</h3>
                        @endif
                    </div>
                    @if($enrollment->grade_points)
                    <div class="text-center">
                        <p class="mb-0">Grade Points: <strong>{{ $enrollment->grade_points }}</strong></p>
                    </div>
                    @endif
                    @else
                    <p class="text-muted text-center">No final grade yet</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if($enrollment->status === 'pending')
                    <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check"></i> Approve Enrollment
                        </button>
                    </form>
                    <form action="{{ route('admin.enrollments.reject', $enrollment) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times"></i> Reject Enrollment
                        </button>
                    </form>
                    @endif

                    @if($enrollment->status === 'enrolled')
                    <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to drop this enrollment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-user-times"></i> Drop Enrollment
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
