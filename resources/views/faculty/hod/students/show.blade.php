@extends('layouts.app')

@section('title', 'Student Profile - ' . $studentProfile->user->first_name)

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-graduate text-primary mr-2"></i>Student Profile
            </h1>
            <p class="text-muted mb-0">Department: {{ $department->name }}</p>
        </div>
        <a href="{{ route('faculty.hod.students.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Students
        </a>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 border-0">
                <div class="card-body text-center pt-5 pb-4">
                    <div class="avatar avatar-xl bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ substr($studentProfile->user->first_name, 0, 1) }}
                    </div>
                    <h4 class="font-weight-bold mb-1">{{ $studentProfile->user->first_name }} {{ $studentProfile->user->last_name }}</h4>
                    <p class="text-muted mb-3">{{ $studentProfile->admission_number ?? 'No ID Assigned' }}</p>
                    
                    @if($studentProfile->status === 'active')
                        <span class="badge badge-success px-3 py-2 mb-3 rounded-pill">Active Student</span>
                    @else
                        <span class="badge badge-secondary px-3 py-2 mb-3 rounded-pill">{{ ucfirst($studentProfile->status) }}</span>
                    @endif

                    <hr>
                    <div class="text-left">
                        <div class="mb-2">
                            <i class="fas fa-envelope fa-fw text-muted mr-2"></i> {{ $studentProfile->user->email }}
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-phone fa-fw text-muted mr-2"></i> {{ $studentProfile->user->phone ?? 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4 border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Programme</div>
                            <div class="h6 text-gray-800">{{ $studentProfile->program->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Current Semester / Year</div>
                            <div class="h6 text-gray-800">
                                Year {{ $studentProfile->year_of_study ?? 'N/A' }} 
                                (Semester {{ $studentProfile->current_semester ?? 'N/A' }})
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Current GPA</div>
                            <div class="h6 text-gray-800">{{ $studentProfile->current_gpa ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Cumulative GPA</div>
                            <div class="h6 text-gray-800">{{ $studentProfile->cumulative_gpa ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Credits Earned</div>
                            <div class="h6 text-gray-800">{{ $studentProfile->total_credits_earned ?? '0' }} / {{ $studentProfile->total_credits_required ?? '0' }}</div>
                            @if($studentProfile->total_credits_required > 0)
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($studentProfile->total_credits_earned / $studentProfile->total_credits_required) * 100 }}%"></div>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Expected Graduation</div>
                            <div class="h6 text-gray-800">{{ $studentProfile->expected_graduation_date ? $studentProfile->expected_graduation_date->format('M Y') : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Guardian Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Name</div>
                            <div class="text-gray-800">{{ $studentProfile->guardian_name ?? 'Not provided' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Contact</div>
                            <div class="text-gray-800">
                                {{ $studentProfile->guardian_phone ?? 'N/A' }} <br>
                                {{ $studentProfile->guardian_email ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
