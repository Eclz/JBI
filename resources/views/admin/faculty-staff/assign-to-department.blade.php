@extends('layouts.app')

@section('title', 'Assign Faculty to Department')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">How to Add Staff to Faculties and Departments</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Step-by-Step Process:</h5>

                            <div class="accordion" id="staffAssignmentAccordion">
                                <!-- Step 1: Create Faculty -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                            <strong>Step 1: Create Academic Faculty (School/College)</strong>
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#staffAssignmentAccordion">
                                        <div class="accordion-body">
                                            <p>First, create an academic faculty (like School of Engineering, College of Arts, etc.)</p>
                                            <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus"></i> Create New Faculty
                                            </a>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    Example: "School of Engineering", "College of Business", "Faculty of Medicine"
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Create Departments -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                            <strong>Step 2: Create Departments under Faculty</strong>
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#staffAssignmentAccordion">
                                        <div class="accordion-body">
                                            <p>Create departments within each faculty</p>
                                            <a href="{{ route('admin.departments.create') }}" class="btn btn-success btn-sm">
                                                <i class="bi bi-plus"></i> Create New Department
                                            </a>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    Example: "Computer Science" under "School of Engineering"
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Add Faculty Staff -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                            <strong>Step 3: Add Faculty Staff Members</strong>
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#staffAssignmentAccordion">
                                        <div class="accordion-body">
                                            <p>Add individual faculty members and assign them to specific departments</p>
                                            <a href="{{ route('admin.faculty-staff.create') }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-person-plus"></i> Add Faculty Staff Member
                                            </a>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    When creating a faculty member, you'll select their department, which automatically assigns them to the parent faculty.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 4: Assign Department Head -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFour">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                            <strong>Step 4: Assign Department Head (Optional)</strong>
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#staffAssignmentAccordion">
                                        <div class="accordion-body">
                                            <p>Assign a faculty member as the head of a department</p>
                                            <a href="{{ route('admin.departments.index') }}" class="btn btn-warning btn-sm">
                                                <i class="bi bi-person-badge"></i> Manage Department Heads
                                            </a>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    Go to Departments list and use the "Assign Head" option for each department.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Current Structure</h6>
                                </div>
                                <div class="card-body">
                                    @if($faculties->count() > 0)
                                        @foreach($faculties as $faculty)
                                            <div class="mb-3">
                                                <strong>{{ $faculty->name }}</strong>
                                                @if($faculty->departments->count() > 0)
                                                    <ul class="list-unstyled ms-3 mt-1">
                                                        @foreach($faculty->departments as $department)
                                                            <li class="mb-1">
                                                                <i class="bi bi-arrow-right"></i> {{ $department->name }}
                                                                <small class="text-muted">
                                                                    ({{ $department->facultyMembers->count() }} staff)
                                                                </small>
                                                                @if($department->headOfDepartment)
                                                                    <br><small class="text-success ms-3">
                                                                        Head: {{ $department->headOfDepartment->name }}
                                                                    </small>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <small class="text-muted ms-3">No departments yet</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No faculties created yet. Start by creating a faculty.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card bg-info text-white mt-3">
                                <div class="card-body">
                                    <h6>Quick Stats</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li>Faculties: {{ $faculties->count() }}</li>
                                        <li>Departments: {{ $departments->count() }}</li>
                                        <li>Faculty Staff: {{ $facultyStaff->count() }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
