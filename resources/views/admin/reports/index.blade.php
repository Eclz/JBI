@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">Reports & Analytics</h1>
            <p class="text-muted">Generate comprehensive reports for different aspects of the university management system</p>
        </div>
    </div>

    <div class="row">
         Enrollment Reports
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded p-3 me-3">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Enrollment Reports</h5>
                            <small class="text-muted">Student enrollment statistics</small>
                        </div>
                    </div>
                    <p class="card-text">View enrollment trends, statistics by department, semester-wise analysis, and enrollment status breakdown.</p>
                    <a href="{{ route('admin.reports.enrollment') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-chart-bar me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

         Financial Reports
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success text-white rounded p-3 me-3">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Financial Reports</h5>
                            <small class="text-muted">Fee collection and payments</small>
                        </div>
                    </div>
                    <p class="card-text">Track fee collections, outstanding payments, payment trends, and financial summaries for accounting.</p>
                    <a href="{{ route('admin.reports.financial') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-chart-line me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

         Academic Performance Reports
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info text-white rounded p-3 me-3">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Academic Performance</h5>
                            <small class="text-muted">Grades and achievements</small>
                        </div>
                    </div>
                    <p class="card-text">Analyze student grades, grade distribution, pass rates, top performers, and academic trends.</p>
                    <a href="{{ route('admin.reports.academic') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-graduation-cap me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

         Attendance Reports
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning text-white rounded p-3 me-3">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Attendance Reports</h5>
                            <small class="text-muted">Class attendance tracking</small>
                        </div>
                    </div>
                    <p class="card-text">Monitor attendance rates, identify students with low attendance, and generate course-wise reports.</p>
                    <a href="{{ route('admin.reports.attendance') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-clipboard-check me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

         Student Reports
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-secondary text-white rounded p-3 me-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Student Reports</h5>
                            <small class="text-muted">Student demographics</small>
                        </div>
                    </div>
                    <p class="card-text">View student demographics, enrollment trends, department distribution, and status breakdowns.</p>
                    <a href="{{ route('admin.reports.students') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-list me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

         Faculty Reports
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger text-white rounded p-3 me-3">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Faculty Reports</h5>
                            <small class="text-muted">Faculty information</small>
                        </div>
                    </div>
                    <p class="card-text">Track faculty members, teaching loads, department distribution, and employment status.</p>
                    <a href="{{ route('admin.reports.faculty') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-user-tie me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

         Course Reports
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-dark text-white rounded p-3 me-3">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Course Reports</h5>
                            <small class="text-muted">Course offerings</small>
                        </div>
                    </div>
                    <p class="card-text">Analyze course offerings, enrollment patterns, popular courses, and department-wise distribution.</p>
                    <a href="{{ route('admin.reports.courses') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-book-open me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

         Fee Structure Reports
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-purple text-white rounded p-3 me-3">
                            <i class="fas fa-file-invoice-dollar fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Fee Structure Reports</h5>
                            <small class="text-muted">Fee structure analysis</small>
                        </div>
                    </div>
                    <p class="card-text">View fee structures, compare fees across departments, and analyze fee components breakdown.</p>
                    <a href="{{ route('admin.reports.fee-structures') }}" class="btn btn-purple btn-sm">
                        <i class="fas fa-money-check-alt me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Report Features</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-filter text-primary me-2"></i>Advanced Filtering</h6>
                            <p class="text-muted small">Filter reports by date range, department, semester, academic year, and more.</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-file-pdf text-danger me-2"></i>PDF Export</h6>
                            <p class="text-muted small">Download any report as a PDF file for printing or sharing.</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-chart-bar text-success me-2"></i>Visual Analytics</h6>
                            <p class="text-muted small">View data with charts and graphs for better insights.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}
.btn-purple {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
}
.btn-purple:hover {
    background-color: #5a32a3;
    border-color: #5a32a3;
    color: white;
}
</style>
@endsection
