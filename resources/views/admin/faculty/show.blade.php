@extends('layouts.app')

@section('title', 'Faculty Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Faculty Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.faculties.index') }}">Faculty Management</a></li>
                            <li class="breadcrumb-item active">{{ $faculty->full_name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.faculty.edit', $faculty) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit Faculty
                    </a>
                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="{{ $faculty->profile_picture_url }}"
                             alt="{{ $faculty->full_name }}"
                             class="rounded-circle"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <h4 class="card-title mb-1">{{ $faculty->full_name }}</h4>
                    <p class="text-muted mb-2">{{ $faculty->facultyProfile->position_title ?? 'Faculty Member' }}</p>
                    <p class="text-muted mb-3">
                        {{ $faculty->facultyProfile->department->name ?? 'No Department Assigned' }}
                    </p>

                    <!-- Status Badges -->
                    <div class="mb-3">
                        @if($faculty->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif

                        @if($faculty->email_verified_at)
                            <span class="badge bg-info">Email Verified</span>
                        @else
                            <span class="badge bg-warning">Email Not Verified</span>
                        @endif

                        @if($faculty->facultyProfile && $faculty->facultyProfile->employment_status)
                            <span class="badge bg-primary">{{ ucfirst($faculty->facultyProfile->employment_status) }}</span>
                        @endif
                    </div>

                    @if($faculty->facultyProfile && $faculty->facultyProfile->employee_id)
                        <p class="text-muted small">Employee ID: {{ $faculty->facultyProfile->employee_id }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill"></i> Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <div>
                                    <a href="mailto:{{ $faculty->email }}" class="text-decoration-none">
                                        {{ $faculty->email }}
                                    </a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Phone</label>
                                <div>
                                    <a href="tel:{{ $faculty->phone }}" class="text-decoration-none">
                                        {{ $faculty->phone ?? 'Not provided' }}
                                    </a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Date of Birth</label>
                                <div>
                                    {{ $faculty->date_of_birth ? $faculty->date_of_birth->format('F j, Y') : 'Not provided' }}
                                    @if($faculty->date_of_birth)
                                        <small class="text-muted">({{ $faculty->date_of_birth->age }} years old)</small>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Gender</label>
                                <div>{{ ucfirst($faculty->gender ?? 'Not specified') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Address</label>
                                <div>{{ $faculty->address ?? 'Not provided' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Emergency Contact</label>
                                <div>{{ $faculty->emergency_contact ?? 'Not provided' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Emergency Phone</label>
                                <div>
                                    @if($faculty->emergency_phone)
                                        <a href="tel:{{ $faculty->emergency_phone }}" class="text-decoration-none">
                                            {{ $faculty->emergency_phone }}
                                        </a>
                                    @else
                                        Not provided
                                    @endif
                                </div>
                            </div>
                            @if($faculty->facultyProfile && $faculty->facultyProfile->hire_date)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Hire Date</label>
                                    <div>
                                        {{ $faculty->facultyProfile->hire_date->format('F j, Y') }}
                                        <small class="text-muted">({{ $faculty->facultyProfile->hire_date->diffForHumans() }})</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Professional Information -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-mortarboard"></i> Qualifications
                    </h5>
                </div>
                <div class="card-body">
                    @if($faculty->facultyProfile && $faculty->facultyProfile->formatted_qualifications)
                        @php
                            $qualifications = $faculty->facultyProfile->formatted_qualifications;
                        @endphp

                        @if(isset($qualifications['highest_degree']))
                            <div class="mb-3">
                                <label class="form-label text-muted">Highest Degree</label>
                                <div>{{ $qualifications['highest_degree'] }}</div>
                            </div>
                        @endif

                        @if(isset($qualifications['institution']))
                            <div class="mb-3">
                                <label class="form-label text-muted">Institution</label>
                                <div>{{ $qualifications['institution'] }}</div>
                            </div>
                        @endif

                        @if(isset($qualifications['graduation_year']))
                            <div class="mb-3">
                                <label class="form-label text-muted">Graduation Year</label>
                                <div>{{ $qualifications['graduation_year'] }}</div>
                            </div>
                        @endif

                        @if(isset($qualifications['specialization']))
                            <div class="mb-3">
                                <label class="form-label text-muted">Specialization</label>
                                <div>{{ $qualifications['specialization'] }}</div>
                            </div>
                        @endif

                        @if(isset($qualifications['certifications']) && is_array($qualifications['certifications']) && count($qualifications['certifications']) > 0)
                            <div class="mb-3">
                                <label class="form-label text-muted">Certifications</label>
                                <div>
                                    @foreach($qualifications['certifications'] as $certification)
                                        <span class="badge bg-secondary me-1">{{ $certification }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No qualification information available.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Experience -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-briefcase"></i> Experience
                    </h5>
                </div>
                <div class="card-body">
                    @if($faculty->facultyProfile && $faculty->facultyProfile->formatted_experience)
                        @php
                            $experience = $faculty->facultyProfile->formatted_experience;
                        @endphp

                        @if(isset($experience['years_of_experience']))
                            <div class="mb-3">
                                <label class="form-label text-muted">Years of Experience</label>
                                <div>{{ $experience['years_of_experience'] }} years</div>
                            </div>
                        @endif

                        @if(isset($experience['previous_positions']) && is_array($experience['previous_positions']) && count($experience['previous_positions']) > 0)
                            <div class="mb-3">
                                <label class="form-label text-muted">Previous Positions</label>
                                <div>
                                    @foreach($experience['previous_positions'] as $position)
                                        <span class="badge bg-info me-1">{{ $position }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(isset($experience['research_interests']) && is_array($experience['research_interests']) && count($experience['research_interests']) > 0)
                            <div class="mb-3">
                                <label class="form-label text-muted">Research Interests</label>
                                <div>
                                    @foreach($experience['research_interests'] as $interest)
                                        <span class="badge bg-success me-1">{{ $interest }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No experience information available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Teaching Courses -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-journal-text"></i> Teaching Courses
                    </h5>
                </div>
                <div class="card-body">
                    @if($faculty->teachingCourses && $faculty->teachingCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Department</th>
                                        <th>Credits</th>
                                        <th>Semester</th>
                                        <th>Enrolled Students</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($faculty->teachingCourses as $course)
                                        <tr>
                                            <td>{{ $course->course_code }}</td>
                                            <td>{{ $course->name }}</td>
                                            <td>{{ $course->department->name ?? 'N/A' }}</td>
                                            <td>{{ $course->credits }}</td>
                                            <td>
                                                @if($course->semester)
                                                    {{ $course->semester->name }}
                                                    @if($course->semester->is_current)
                                                        <span class="badge bg-primary">Current</span>
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $course->enrollments ? $course->enrollments->count() : 0 }} students
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">
                                                    View Course
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No courses assigned to this faculty member.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear"></i> Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Account Created</label>
                                <div>
                                    {{ $faculty->created_at->format('F j, Y g:i A') }}
                                    <small class="text-muted d-block">{{ $faculty->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Last Updated</label>
                                <div>
                                    {{ $faculty->updated_at->format('F j, Y g:i A') }}
                                    <small class="text-muted d-block">{{ $faculty->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Last Login</label>
                                <div>
                                    @if($faculty->last_login_at)
                                        {{ $faculty->last_login_at->format('F j, Y g:i A') }}
                                        <small class="text-muted d-block">{{ $faculty->last_login_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Never logged in</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($faculty->must_change_password)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            This faculty member must change their password on next login.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endpush
