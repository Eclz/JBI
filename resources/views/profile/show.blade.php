@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-person me-2"></i>My Profile
            </h1>
            <p class="text-muted mb-0">View and manage your profile information</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Profile
        </a>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <span class="h2 mb-0">{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                    <p class="text-muted mb-3">{{ ucfirst($user->role) }}</p>

                    @if($user->role === 'student' && $user->studentProfile)
                        <div class="mb-3">
                            <span class="badge bg-info">{{ $user->studentProfile->student_id }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-success">{{ $user->studentProfile->program }}</span>
                        </div>
                    @elseif($user->role === 'faculty' && $user->facultyProfile)
                        <div class="mb-3">
                            <span class="badge bg-info">{{ $user->facultyProfile->employee_id }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-success">{{ $user->facultyProfile->position }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-badge me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">First Name</label>
                            <p class="mb-0">{{ $user->first_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Last Name</label>
                            <p class="mb-0">{{ $user->last_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Phone</label>
                            <p class="mb-0">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Date of Birth</label>
                            <p class="mb-0">{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Gender</label>
                            <p class="mb-0">{{ $user->gender ? ucfirst($user->gender) : 'Not provided' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Address</label>
                            <p class="mb-0">{{ $user->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic/Professional Information -->
            @if($user->role === 'student' && $user->studentProfile)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-mortarboard me-2"></i>Academic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Student ID</label>
                                <p class="mb-0">{{ $user->studentProfile->student_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Department</label>
                                <p class="mb-0">{{ $user->studentProfile->department->name ?? 'Not assigned' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Program</label>
                                <p class="mb-0">{{ $user->studentProfile->program }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Academic Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $user->studentProfile->academic_status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($user->studentProfile->academic_status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Admission Date</label>
                                <p class="mb-0">{{ $user->studentProfile->admission_date ? $user->studentProfile->admission_date->format('M d, Y') : 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Expected Graduation</label>
                                <p class="mb-0">{{ $user->studentProfile->expected_graduation_date ? $user->studentProfile->expected_graduation_date->format('M d, Y') : 'Not set' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Current GPA</label>
                                <p class="mb-0">{{ $user->studentProfile->current_gpa ? number_format($user->studentProfile->current_gpa, 2) : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Total Credits</label>
                                <p class="mb-0">{{ $user->studentProfile->total_credits_earned ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($user->role === 'faculty' && $user->facultyProfile)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-briefcase me-2"></i>Professional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Employee ID</label>
                                <p class="mb-0">{{ $user->facultyProfile->employee_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Department</label>
                                <p class="mb-0">{{ $user->facultyProfile->department->name ?? 'Not assigned' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Position</label>
                                <p class="mb-0">{{ $user->facultyProfile->position }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Employment Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $user->facultyProfile->employment_status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($user->facultyProfile->employment_status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Hire Date</label>
                                <p class="mb-0">{{ $user->facultyProfile->hire_date ? $user->facultyProfile->hire_date->format('M d, Y') : 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Office</label>
                                <p class="mb-0">{{ $user->facultyProfile->office ?? 'Not assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Emergency Contact -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-telephone me-2"></i>Emergency Contact
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Contact Name</label>
                            <p class="mb-0">{{ $user->emergency_contact_name ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Contact Phone</label>
                            <p class="mb-0">{{ $user->emergency_contact_phone ?? 'Not provided' }}</p>
                        </div>
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
    width: 100px;
    height: 100px;
    font-size: 2rem;
}
</style>
@endpush
