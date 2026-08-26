@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(Auth::check() && Auth::user()->isStudent())
        @include('partials.student-header-bar')
    @endif
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
                        @if($user->profile_picture)
                            <img src="{{ $user->profile_picture_url }}" alt="Avatar" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <span class="h2 mb-0">{{ $user->initials }}</span>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                    <p class="text-muted mb-3">{{ ucfirst($user->role) }}</p>

                    @if($user->role === 'student' && $user->studentProfile)
                        <div class="mb-3">
                            <span class="badge bg-primary px-3 py-2 text-uppercase fs-6 shadow-sm"><i class="bi bi-person-badge me-1"></i>{{ $user->studentProfile->student_id }}</span>
                        </div>
                        <div class="mt-3 p-3 text-start rounded-3 bg-white border border-primary border-2 shadow-sm">
                            <small class="text-uppercase fw-bold text-muted d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;"><i class="bi bi-journal-bookmark me-1 text-primary"></i>ENROLLED DEGREE PROGRAMME</small>
                            <h6 class="fw-bold text-primary mb-1" style="font-size: 0.95rem; line-height: 1.4;">{{ $user->studentProfile->program }}</h6>
                            <small class="text-muted"><i class="bi bi-building me-1"></i>{{ $user->studentProfile->department->name ?? 'School of Computing & IT' }}</small>
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
                    <div class="card-header bg-white border-bottom border-2 border-primary">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="bi bi-mortarboard me-2"></i>Academic Journey & Progress Tracker
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded text-center border">
                                    <small class="text-muted text-uppercase d-block fw-semibold mb-1">Current Year of Study</small>
                                    <span class="fs-4 fw-bold text-primary">Year {{ $user->studentProfile->year_of_study ?? 1 }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded text-center border">
                                    <small class="text-muted text-uppercase d-block fw-semibold mb-1">Current Semester</small>
                                    <span class="fs-4 fw-bold text-primary">Semester {{ ($user->studentProfile->current_semester ?? 1) == 1 ? 'I' : 'II' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded text-center border">
                                    <small class="text-muted text-uppercase d-block fw-semibold mb-1">Academic Status</small>
                                    <span class="badge bg-primary px-3 py-2 text-uppercase fs-6">NORMAL PROGRESS</span>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Journey Timeline -->
                        <h6 class="fw-bold text-dark text-uppercase small mb-3"><i class="bi bi-diagram-3 me-2 text-primary"></i>Academic Journey Roadmap</h6>
                        <div class="position-relative py-3 px-2">
                            <div class="row text-center g-2 position-relative" style="z-index: 2;">
                                @php
                                    $cy = $user->studentProfile->year_of_study ?? 1;
                                    $cs = $user->studentProfile->current_semester ?? 1;
                                @endphp
                                @for($y = 1; $y <= 4; $y++)
                                    @for($s = 1; $s <= 2; $s++)
                                        @php
                                            $isPast = ($y < $cy) || ($y == $cy && $s < $cs);
                                            $isCurrent = ($y == $cy && $s == $cs);
                                        @endphp
                                        <div class="col">
                                            <div class="p-2 rounded border {{ $isCurrent ? 'bg-primary text-white shadow-sm' : ($isPast ? 'bg-primary bg-opacity-10 text-primary border-primary' : 'bg-light text-muted') }}">
                                                <div class="fw-bold" style="font-size: 0.75rem;">Y{{ $y }} S{{ $s == 1 ? 'I' : 'II' }}</div>
                                                <div style="font-size: 0.65rem;" class="mt-1">
                                                    @if($isCurrent)
                                                        <span class="badge bg-warning text-dark px-1">CURRENT</span>
                                                    @elseif($isPast)
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    @else
                                                        <i class="bi bi-circle"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                @endfor
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="col-12 mb-2">
                            <div class="p-3.5 bg-light rounded-3 border-start border-4 border-primary border shadow-sm">
                                <small class="text-muted text-uppercase d-block fw-bold mb-1" style="letter-spacing: 0.5px;"><i class="bi bi-award-fill text-primary me-1"></i>ENROLLED DEGREE PROGRAMME</small>
                                <h5 class="fw-bold text-dark mb-1" style="color: #1e3a8a !important;">{{ $user->studentProfile->program }}</h5>
                                <div class="d-flex align-items-center flex-wrap gap-3 small text-muted">
                                    <span><i class="bi bi-building me-1"></i>Department: <strong>{{ $user->studentProfile->department->name ?? 'School of Computing & IT' }}</strong></span>
                                    <span><i class="bi bi-mortarboard me-1"></i>Degree Level: <strong>Undergraduate</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Student ID</label>
                                <p class="mb-0 fw-bold">{{ $user->studentProfile->student_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Admission Date</label>
                                <p class="mb-0">{{ $user->studentProfile->admission_date ? $user->studentProfile->admission_date->format('M d, Y') : 'Aug 15, 2026' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Expected Graduation</label>
                                <p class="mb-0">{{ $user->studentProfile->expected_graduation_date ? $user->studentProfile->expected_graduation_date->format('M d, Y') : 'Jun 30, 2028' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Current CGPA</label>
                                <p class="mb-0 fw-bold text-primary">{{ $user->studentProfile->current_gpa ? number_format($user->studentProfile->current_gpa, 2) : '3.55' }}</p>
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
