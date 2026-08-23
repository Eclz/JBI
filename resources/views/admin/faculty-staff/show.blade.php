@extends('layouts.app')

@section('title', 'Faculty Staff Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Faculty Staff Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.faculty-staff.index') }}">Faculty Staff</a></li>
                    <li class="breadcrumb-item active">{{ $facultyStaff->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.faculty-staff.edit', $facultyStaff) }}" class="btn btn-primary">
                <i class="fa fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.faculty-staff.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if(!$facultyStaff->is_active)
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i>
            This faculty member is currently inactive.
        </div>
    @endif

    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-user"></i> Personal Information
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($facultyStaff->profile_picture)
                        <img src="{{ $facultyStaff->profile_picture_url }}"
                             alt="{{ $facultyStaff->name }}"
                             class="rounded-circle mb-3"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 120px; height: 120px;">
                            <i class="fa fa-user fa-3x text-white"></i>
                        </div>
                    @endif

                    <h4>{{ $facultyStaff->name }}</h4>
                    <p class="text-muted">{{ $facultyStaff->facultyProfile->position ?? 'Faculty Member' }}</p>

                    <div class="row text-center mt-3">
                        <div class="col-4">
                            <div class="border-end">
                                <h6 class="mb-0">{{ $teachingStats['total_courses'] }}</h6>
                                <small class="text-muted">Total Courses</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h6 class="mb-0">{{ $teachingStats['active_courses'] }}</h6>
                                <small class="text-muted">Active Courses</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0">{{ $teachingStats['total_students'] }}</h6>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-address-book"></i> Contact Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <div>{{ $facultyStaff->email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Phone</label>
                        <div>{{ $facultyStaff->phone ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Address</label>
                        <div>{{ $facultyStaff->address ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Emergency Contact</label>
                        <div>{{ $facultyStaff->emergency_contact ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted">Emergency Phone</label>
                        <div>{{ $facultyStaff->emergency_phone ?? 'Not provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="col-lg-8">
            <!-- Employment Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-briefcase"></i> Employment Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Employee ID</label>
                                <div class="fw-bold">{{ $facultyStaff->facultyProfile->employee_id ?? 'Not assigned' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Department</label>
                                <div>
                                    @if($facultyStaff->facultyProfile && $facultyStaff->facultyProfile->department)
                                        <a href="{{ route('admin.departments.show', $facultyStaff->facultyProfile->department) }}" class="text-decoration-none">
                                            {{ $facultyStaff->facultyProfile->department->name }}
                                        </a>
                                        @if($facultyStaff->facultyProfile->department->faculty)
                                            <br><small class="text-muted">{{ $facultyStaff->facultyProfile->department->faculty->name }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Position</label>
                                <div>{{ $facultyStaff->facultyProfile->position ?? 'Not specified' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Employment Type</label>
                                <div>
                                    @if($facultyStaff->facultyProfile)
                                        <span class="badge bg-info">
                                            {{ ucwords(str_replace('_', ' ', $facultyStaff->facultyProfile->employment_type)) }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Employment Status</label>
                                <div>
                                    @if($facultyStaff->facultyProfile)
                                        @php
                                            $statusClass = match($facultyStaff->facultyProfile->employment_status) {
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'on_leave' => 'warning',
                                                'terminated' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucwords(str_replace('_', ' ', $facultyStaff->facultyProfile->employment_status)) }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Joining Date</label>
                                <div>{{ $facultyStaff->facultyProfile->joining_date ? $facultyStaff->facultyProfile->joining_date->format('M d, Y') : 'Not specified' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Years of Experience</label>
                                <div>{{ $facultyStaff->facultyProfile->years_of_experience ?? 0 }} years</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Account Status</label>
                                <div>
                                    <span class="badge bg-{{ $facultyStaff->is_active ? 'success' : 'danger' }}">
                                        {{ $facultyStaff->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Qualifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-graduation-cap"></i> Academic Qualifications
                    </h6>
                </div>
                <div class="card-body">
                    @if($facultyStaff->facultyProfile && $facultyStaff->facultyProfile->qualifications)
                        @php $qualifications = $facultyStaff->facultyProfile->qualifications; @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Highest Degree</label>
                                    <div>{{ $qualifications['highest_degree'] ?? $facultyStaff->facultyProfile->qualification ?? 'Not specified' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Institution</label>
                                    <div>{{ $qualifications['institution'] ?? 'Not specified' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Graduation Year</label>
                                    <div>{{ $qualifications['graduation_year'] ?? 'Not specified' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Specialization</label>
                                    <div>{{ $qualifications['specialization'] ?? $facultyStaff->facultyProfile->specialization ?? 'Not specified' }}</div>
                                </div>
                            </div>
                        </div>

                        @if(isset($qualifications['certifications']) && is_array($qualifications['certifications']) && count($qualifications['certifications']) > 0)
                            <div class="mb-3">
                                <label class="form-label text-muted">Certifications</label>
                                <div>
                                    @foreach($qualifications['certifications'] as $certification)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $certification }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No qualification information available.</p>
                    @endif
                </div>
            </div>

            <!-- Teaching Information -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-chalkboard-teacher me-1"></i> Teaching Information
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted d-none d-md-inline">Current Semester: {{ $teachingStats['current_semester_courses'] }} courses</small>
                        <a href="{{ route('admin.faculty-staff.edit', $facultyStaff) }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.78rem;">
                            <i class="fa fa-tasks me-1"></i>Assign Courses
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($facultyStaff->taughtCourses && $facultyStaff->taughtCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Department</th>
                                        <th>Semester</th>
                                        <th>Students</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facultyStaff->taughtCourses->take(10) as $course)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.courses.show', $course) }}" class="text-decoration-none">
                                                    {{ $course->name }}
                                                </a>
                                                <br><small class="text-muted">{{ $course->code }}</small>
                                            </td>
                                            <td>{{ $course->department->name ?? 'N/A' }}</td>
                                            <td>{{ $course->semester->name ?? 'N/A' }}</td>
                                            <td>{{ $course->enrollments->count() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $course->is_active ? 'success' : 'secondary' }}">
                                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($facultyStaff->taughtCourses->count() > 10)
                            <div class="text-center mt-3">
                                <small class="text-muted">Showing 10 of {{ $facultyStaff->taughtCourses->count() }} courses</small>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No courses assigned yet.</p>
                    @endif
                </div>
            </div>

            <!-- Additional Information -->
            @if($facultyStaff->facultyProfile && ($facultyStaff->facultyProfile->bio || $facultyStaff->facultyProfile->linkedin_profile || $facultyStaff->facultyProfile->personal_website))
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fa fa-info-circle"></i> Additional Information
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($facultyStaff->facultyProfile->bio)
                            <div class="mb-3">
                                <label class="form-label text-muted">Biography</label>
                                <div>{{ $facultyStaff->facultyProfile->bio }}</div>
                            </div>
                        @endif

                        @if($facultyStaff->facultyProfile->linkedin_profile || $facultyStaff->facultyProfile->personal_website)
                            <div class="mb-3">
                                <label class="form-label text-muted">Professional Links</label>
                                <div>
                                    @if($facultyStaff->facultyProfile->linkedin_profile)
                                        <a href="{{ $facultyStaff->facultyProfile->linkedin_profile }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fab fa-linkedin"></i> LinkedIn
                                        </a>
                                    @endif
                                    @if($facultyStaff->facultyProfile->personal_website)
                                        <a href="{{ $facultyStaff->facultyProfile->personal_website }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-globe"></i> Website
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($facultyStaff->facultyProfile->experience && isset($facultyStaff->facultyProfile->experience['research_interests']) && is_array($facultyStaff->facultyProfile->experience['research_interests']) && count($facultyStaff->facultyProfile->experience['research_interests']) > 0)
                            <div class="mb-0">
                                <label class="form-label text-muted">Research Interests</label>
                                <div>
                                    @foreach($facultyStaff->facultyProfile->experience['research_interests'] as $interest)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $interest }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Quick Actions</h6>
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.faculty-staff.edit', $facultyStaff) }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Edit Profile
                        </a>
                        @if($facultyStaff->facultyProfile && $facultyStaff->facultyProfile->department)
                            <a href="{{ route('admin.departments.show', $facultyStaff->facultyProfile->department) }}" class="btn btn-outline-secondary">
                                <i class="fa fa-building"></i> View Department
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-{{ $facultyStaff->is_active ? 'danger' : 'success' }}"
                                onclick="toggleStatus({{ $facultyStaff->id }})">
                            <i class="fa fa-{{ $facultyStaff->is_active ? 'ban' : 'check' }}"></i>
                            {{ $facultyStaff->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="resetPassword({{ $facultyStaff->id }})">
                            <i class="fa fa-key"></i> Reset Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStatus(userId) {
    if (confirm('Are you sure you want to change this faculty member\'s status?')) {
        fetch(`/admin/faculty-staff/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to update status'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status');
        });
    }
}

function resetPassword(userId) {
    if (confirm('Are you sure you want to reset this faculty member\'s password?')) {
        fetch(`/admin/users/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Password reset successfully. New password: ' + data.new_password);
            } else {
                alert('Error: ' + (data.error || 'Failed to reset password'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resetting the password');
        });
    }
}
</script>
@endpush
@endsection
