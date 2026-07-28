@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-person me-2"></i>{{ $student->first_name }} {{ $student->last_name }}
        </h1>
        <p class="text-muted mb-0">Student ID: {{ $student->studentProfile->student_id }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Students
        </a>
        @can('update', $student->studentProfile)
            <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Edit Student
            </a>
        @endcan
        <a href="{{ route('students.transcript', $student) }}" class="btn btn-info">
            <i class="bi bi-file-text me-2"></i>Transcript
        </a>
    </div>
</div>

<div class="row">
    <!-- Personal Information -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-badge me-2"></i>Personal Information
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <span class="h3 mb-0">{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                    </div>
                    <h5 class="mb-1">{{ $student->first_name }} {{ $student->last_name }}</h5>
                    <p class="text-muted">{{ $student->studentProfile->program }}</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted">Email</label>
                        <p class="mb-0">{{ $student->email }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Phone</label>
                        <p class="mb-0">{{ $student->phone }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Date of Birth</label>
                        <p class="mb-0">{{ $student->date_of_birth->format('M d, Y') }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Gender</label>
                        <p class="mb-0">{{ ucfirst($student->gender) }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Status</label>
                        <p class="mb-0">
                            @php
                                $statusColors = [
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    'graduated' => 'primary',
                                    'suspended' => 'danger'
                                ];
                                $status = $student->studentProfile->academic_status;
                            @endphp
                            <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Address</label>
                        <p class="mb-0">{{ $student->address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Information -->
    <div class="col-lg-8 mb-4">
        <div class="row">
            <!-- Academic Summary -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-mortarboard me-2"></i>Academic Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h2 text-primary mb-1">{{ $academicSummary['total_credits'] }}</div>
                                    <div class="text-muted">Total Credits</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h2 text-success mb-1">
                                        {{ number_format($academicSummary['current_gpa'], 2) }}
                                    </div>
                                    <div class="text-muted">Current GPA</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h2 text-info mb-1">
                                        {{ number_format($academicSummary['cumulative_gpa'], 2) }}
                                    </div>
                                    <div class="text-muted">Cumulative GPA</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h2 text-warning mb-1">{{ $currentEnrollments->count() }}</div>
                                    <div class="text-muted">Current Courses</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Enrollments -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-book me-2"></i>Current Enrollments
                        </h5>
                        <span class="badge bg-primary">{{ $currentEnrollments->count() }} courses</span>
                    </div>
                    <div class="card-body">
                        @if($currentEnrollments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Credits</th>
                                            <th>Instructor</th>
                                            <th>Schedule</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($currentEnrollments as $enrollment)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-primary">
                                                        {{ $enrollment->course->code }}
                                                    </span>
                                                </td>
                                                <td>{{ $enrollment->course->name }}</td>
                                                <td>{{ $enrollment->course->credits }}</td>
                                                <td>{{ $enrollment->course->instructor->first_name ?? 'TBA' }} {{ $enrollment->course->instructor->last_name ?? '' }}</td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $enrollment->course->schedule_days }} {{ $enrollment->course->schedule_time }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-book display-4 text-muted"></i>
                                <p class="text-muted mt-2">No current enrollments</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-credit-card me-2"></i>Financial Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="h4 text-primary mb-1">{{ $currencyCode }} {{ number_format($financialSummary['total_fees'], 2) }}</div>
                                    <div class="text-muted">Total Fees</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="h4 text-success mb-1">{{ $currencyCode }} {{ number_format($financialSummary['paid_amount'], 2) }}</div>
                                    <div class="text-muted">Paid Amount</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="h4 {{ $financialSummary['outstanding'] > 0 ? 'text-danger' : 'text-success' }} mb-1">
                                        {{ $currencyCode }} {{ number_format($financialSummary['outstanding'], 2) }}
                                    </div>
                                    <div class="text-muted">Outstanding Balance</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Emergency Contact Information -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-telephone me-2"></i>Emergency Contact
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted">Name</label>
                        <p class="mb-0">{{ $student->emergency_contact_name }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Phone</label>
                        <p class="mb-0">{{ $student->emergency_contact_phone }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>Guardian Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted">Guardian Name</label>
                        <p class="mb-0">{{ $student->studentProfile->guardian_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Phone</label>
                        <p class="mb-0">{{ $student->studentProfile->guardian_phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Email</label>
                        <p class="mb-0">{{ $student->studentProfile->guardian_email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-lg {
    width: 80px;
    height: 80px;
    font-size: 1.5rem;
}
</style>
@endpush
