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

    <!-- Department Budget & Financial Alignment Overview -->
    <div class="row mt-1 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8fbfd 100%); border-left: 5px solid #198754 !important;">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-wallet2 me-1"></i> Department Financials
                                </span>
                                @if($budget)
                                    <span class="badge bg-light text-muted border font-monospace">
                                        {{ $budget->budget_code }} ({{ $budget->academic_year }})
                                    </span>
                                @endif
                            </div>
                            <h4 class="fw-bold mb-1 text-dark">Department Budget & Expense Alignment</h4>
                            <p class="text-muted mb-0 small">Track expenditures aligned against the departmental budget allocation.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('faculty.hod.expenses.index') }}" class="btn btn-outline-success fw-bold px-3">
                                <i class="bi bi-list-ul me-1"></i> View Expenses
                            </a>
                            <a href="{{ route('faculty.hod.expenses.index') }}#record" class="btn btn-success fw-bold px-3 shadow-sm">
                                <i class="bi bi-plus-circle me-1"></i> Record Expense
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mt-3 pt-3 border-top">
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded-3 border">
                                <small class="text-muted d-block fw-semibold text-uppercase">Allocated Budget</small>
                                <span class="fs-4 fw-bold text-dark">{{ $currencyCode }} {{ number_format($allocatedBudget, 2) }}</span>
                                <small class="text-muted d-block mt-1">Formal budget assigned by Finance</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded-3 border">
                                <small class="text-muted d-block fw-semibold text-uppercase">Total Spent (Expenses)</small>
                                <span class="fs-4 fw-bold text-danger">{{ $currencyCode }} {{ number_format($totalExpenses, 2) }}</span>
                                <small class="text-muted d-block mt-1">Recorded departmental expenditures</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded-3 border">
                                <small class="text-muted d-block fw-semibold text-uppercase">Remaining Available</small>
                                <span class="fs-4 fw-bold {{ $remainingBudget <= 0 && $allocatedBudget > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $currencyCode }} {{ number_format($remainingBudget, 2) }}
                                </span>
                                <small class="text-muted d-block mt-1">Available for departmental expenses</small>
                            </div>
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
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                            <a href="{{ route('faculty.hod.students.index') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                                    <h6 class="text-dark font-weight-bold">Students</h6>
                                    <p class="text-muted small px-2">Academic progress & rosters.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                            <a href="{{ route('faculty.hod.department.lecturers') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="fas fa-chalkboard-teacher fa-3x text-success mb-3"></i>
                                    <h6 class="text-dark font-weight-bold">Faculty</h6>
                                    <p class="text-muted small px-2">Department lecturers.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                            <a href="{{ route('faculty.hod.department.programs') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="fas fa-graduation-cap fa-3x text-info mb-3"></i>
                                    <h6 class="text-dark font-weight-bold">Programmes</h6>
                                    <p class="text-muted small px-2">Departmental curriculum.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                            <a href="{{ route('faculty.hod.department.courses') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="fas fa-book fa-3x text-warning mb-3"></i>
                                    <h6 class="text-dark font-weight-bold">Courses</h6>
                                    <p class="text-muted small px-2">Lecturer assignments.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                            <a href="{{ route('faculty.hod.expenses.index') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all" style="border-bottom: 3px solid #198754 !important;">
                                    <i class="bi bi-wallet2 fs-1 text-success mb-3 d-block"></i>
                                    <h6 class="text-dark font-weight-bold">Expenses & Budget</h6>
                                    <p class="text-muted small px-2">Record & track expenses.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                            <a href="{{ route('faculty.hod.operations.index') }}" class="text-decoration-none">
                                <div class="card bg-light border-0 h-100 py-4 hover-shadow transition-all">
                                    <i class="bi bi-briefcase fs-1 text-danger mb-3 d-block"></i>
                                    <h6 class="text-dark font-weight-bold">Operations</h6>
                                    <p class="text-muted small px-2">Leaves & moderation.</p>
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
