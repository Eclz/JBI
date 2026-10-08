@extends('layouts.app')

@section('title', 'Department Overview - ' . $department->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building text-primary mr-2"></i>{{ $department->name }} Overview
        </h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Students Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Enrolled Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lecturers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Faculty Members</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLecturers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Academic Programmes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPrograms }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-award fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Courses Offered</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCourses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links Row -->
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card shadow mb-4 border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Management</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('faculty.hod.students.index') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="fas fa-users-class fa-3x text-primary mb-3"></i>
                                    <h5 class="text-dark font-weight-bold">Manage Students</h5>
                                    <p class="text-muted small px-3">View and monitor student academic progress.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('faculty.hod.department.lecturers') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="fas fa-chalkboard-teacher fa-3x text-success mb-3"></i>
                                    <h5 class="text-dark font-weight-bold">Manage Faculty</h5>
                                    <p class="text-muted small px-3">View lecturers assigned to your department.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('faculty.hod.department.programs') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="fas fa-graduation-cap fa-3x text-info mb-3"></i>
                                    <h5 class="text-dark font-weight-bold">Programmes</h5>
                                    <p class="text-muted small px-3">View programmes offered by the department.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('faculty.hod.department.courses') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="fas fa-book fa-3x text-warning mb-3"></i>
                                    <h5 class="text-dark font-weight-bold">Courses</h5>
                                    <p class="text-muted small px-3">View and manage departmental courses.</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-3px);
}
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection
