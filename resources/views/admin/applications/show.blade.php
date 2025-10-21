<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - JBI University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .application-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        .info-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .info-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
        }
        .document-link {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.5rem 1rem;
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 0.25rem;
            text-decoration: none;
            color: #1976d2;
        }
        .document-link:hover {
            background-color: #bbdefb;
            color: #0d47a1;
        }
        .action-buttons {
            position: sticky;
            top: 20px;
            z-index: 100;
        }
    </style>
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="application-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fa fa-user-graduate me-2"></i>
                        {{ $user->name }}
                    </h1>
                    <p class="mb-0 opacity-75">
                        <i class="fa fa-envelope me-2"></i>{{ $user->email }}
                        <span class="ms-3">
                            <i class="fa fa-calendar me-2"></i>Applied: {{ $user->created_at->format('M d, Y') }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    @if($user->studentProfile)
                        <span class="badge status-badge
                            @if($user->studentProfile->application_status === 'submitted') bg-warning
                            @elseif($user->studentProfile->application_status === 'under_review') bg-info
                            @elseif($user->studentProfile->application_status === 'approved') bg-success
                            @elseif($user->studentProfile->application_status === 'rejected') bg-danger
                            @else bg-secondary @endif">
                            {{ ucfirst(str_replace('_', ' ', $user->studentProfile->application_status ?? 'pending')) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-user me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Full Name:</strong> {{ $user->full_name }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Phone:</strong> {{ $user->phone }}</p>
                                <p><strong>Date of Birth:</strong> {{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                                <p><strong>Gender:</strong> {{ ucfirst($user->gender ?? 'Not specified') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Address:</strong><br>{{ $user->address }}</p>
                                <p><strong>Emergency Contact:</strong> {{ $user->emergency_contact }}</p>
                                <p><strong>Emergency Phone:</strong> {{ $user->emergency_phone }}</p>
                                <p><strong>Email Verified:</strong>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Yes</span>
                                        <small class="text-muted">({{ $user->email_verified_at->format('M d, Y H:i') }})</small>
                                    @else
                                        <span class="badge bg-warning">No</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->studentProfile)
                <!-- Academic Information -->
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-graduation-cap me-2"></i>Academic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Program:</strong> {{ $user->studentProfile->program }}</p>
                                <p><strong>Specialization:</strong> {{ $user->studentProfile->specialization ?? 'Not specified' }}</p>
                                <p><strong>Department:</strong> {{ $user->studentProfile->department->name ?? 'Not assigned' }}</p>
                                <p><strong>Admission Number:</strong> {{ $user->studentProfile->admission_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Expected Graduation:</strong> {{ $user->studentProfile->expected_graduation_date ? $user->studentProfile->expected_graduation_date->format('M Y') : 'Not calculated' }}</p>
                                <p><strong>Current Status:</strong>
                                    <span class="badge bg-secondary">{{ ucfirst($user->studentProfile->status) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Previous Education -->
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-school me-2"></i>Previous Education</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Previous School:</strong> {{ $user->studentProfile->previous_school }}</p>
                                <p><strong>School Address:</strong> {{ $user->studentProfile->previous_school_address ?? 'Not provided' }}</p>
                                <p><strong>Graduation Year:</strong> {{ $user->studentProfile->graduation_year ?? 'Not provided' }}</p>
                                <p><strong>Previous GPA:</strong> {{ $user->studentProfile->previous_gpa ?? 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                @if($user->studentProfile->qualifications)
                                    <p><strong>Test Scores:</strong></p>
                                    <ul class="list-unstyled ms-3">
                                        @if(isset($user->studentProfile->qualifications['sat_score']) && $user->studentProfile->qualifications['sat_score'])
                                            <li>SAT: {{ $user->studentProfile->qualifications['sat_score'] }}</li>
                                        @endif
                                        @if(isset($user->studentProfile->qualifications['act_score']) && $user->studentProfile->qualifications['act_score'])
                                            <li>ACT: {{ $user->studentProfile->qualifications['act_score'] }}</li>
                                        @endif
                                        @if(isset($user->studentProfile->qualifications['toefl_score']) && $user->studentProfile->qualifications['toefl_score'])
                                            <li>TOEFL: {{ $user->studentProfile->qualifications['toefl_score'] }}</li>
                                        @endif
                                        @if(isset($user->studentProfile->qualifications['ielts_score']) && $user->studentProfile->qualifications['ielts_score'])
                                            <li>IELTS: {{ $user->studentProfile->qualifications['ielts_score'] }}</li>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guardian Information -->
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-users me-2"></i>Guardian Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Guardian Name:</strong> {{ $user->studentProfile->guardian_name }}</p>
                                <p><strong>Guardian Phone:</strong> {{ $user->studentProfile->guardian_phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Guardian Email:</strong> {{ $user->studentProfile->guardian_email ?? 'Not provided' }}</p>
                                <p><strong>Guardian Address:</strong><br>{{ $user->studentProfile->guardian_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                @if($user->studentProfile->documents && count($user->studentProfile->documents) > 0)
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-file-alt me-2"></i>Submitted Documents</h5>
                    </div>
                    <div class="card-body">
                        @foreach($user->studentProfile->documents as $index => $document)
                            <a href="{{ asset('storage/' . $document) }}" target="_blank" class="document-link">
                                <i class="fa fa-download me-2"></i>Document {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Application Notes -->
                @if($user->studentProfile->application_notes)
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-sticky-note me-2"></i>Application Notes</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $user->studentProfile->application_notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Admin Notes -->
                @if($user->studentProfile->notes)
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-clipboard me-2"></i>Admin Notes</h5>
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-line;">{{ $user->studentProfile->notes }}</div>
                    </div>
                </div>
                @endif
                @endif
            </div>

            <!-- Action Panel -->
            <div class="col-lg-4">
                <div class="action-buttons">
                    @if($user->studentProfile && $user->studentProfile->application_status === 'submitted')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-cogs me-2"></i>Actions</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.applications.approve', $user->id) }}" method="POST" class="mb-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                                    <textarea name="notes" id="approval_notes" class="form-control" rows="3" placeholder="Add any notes for approval..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to approve this application?')">
                                    <i class="fa fa-check me-2"></i>Approve Application
                                </button>
                            </form>

                            <form action="{{ route('admin.applications.reject', $user->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="rejection_notes" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                    <textarea name="notes" id="rejection_notes" class="form-control" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to reject this application?')">
                                    <i class="fa fa-times me-2"></i>Reject Application
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="card">
                        <div class="card-body text-center">
                            <p class="text-muted">
                                @if($user->studentProfile->application_status === 'approved')
                                    <i class="fa fa-check-circle text-success fa-2x mb-2"></i><br>
                                    Application has been approved
                                @elseif($user->studentProfile->application_status === 'rejected')
                                    <i class="fa fa-times-circle text-danger fa-2x mb-2"></i><br>
                                    Application has been rejected
                                @else
                                    <i class="fa fa-clock text-warning fa-2x mb-2"></i><br>
                                    Application is under review
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- Quick Info -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fa fa-info-circle me-2"></i>Quick Info</h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <p><strong>Application ID:</strong> {{ $user->id }}</p>
                                <p><strong>Submitted:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                                <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}</p>
                                @if($user->studentProfile)
                                <p><strong>Department:</strong> {{ $user->studentProfile->department->name ?? 'Not assigned' }}</p>
                                @endif
                            </small>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-primary w-100">
                                <i class="fa fa-arrow-left me-2"></i>Back to Applications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @endsection
</body>
</html>
