@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Comprehensive admin dashboard with real-time statistics --}}

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Admin Dashboard</h1>
        <div class="text-muted">
            <i class="bi bi-calendar"></i> {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Key Statistics Cards --}}
    <div class="row g-4 mb-4">
        {{-- Students Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">Total Students</p>
                            <h2 class="mb-0">{{ number_format($totalStudents) }}</h2>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> {{ $activeStudents }} active
                            </small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Faculty Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">Total Faculty</p>
                            <h2 class="mb-0">{{ number_format($totalFaculty) }}</h2>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> {{ $activeFaculty }} active
                            </small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-person-badge text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Courses Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">Total Courses</p>
                            <h2 class="mb-0">{{ number_format($totalCourses) }}</h2>
                            <small class="text-info">
                                <i class="bi bi-diagram-3"></i> {{ $totalDepartments }} departments
                            </small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-book text-info fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue Card --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">Total Revenue</p>
                            <h2 class="mb-0">{{ $currencyCode }} {{ number_format($totalRevenue, 2) }}</h2>
                            <small class="text-warning">
                                <i class="bi bi-clock-history"></i> {{ $currencyCode }} {{ number_format($pendingRevenue, 2) }} pending
                            </small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-cash-stack text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Statistics --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Enrollments</p>
                            <h4 class="mb-0">{{ number_format($totalEnrollments) }}</h4>
                        </div>
                        <i class="bi bi-clipboard-check text-primary fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Average Attendance</p>
                            <h4 class="mb-0">{{ number_format($averageAttendance, 1) }}%</h4>
                        </div>
                        <i class="bi bi-calendar-check text-success fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Average GPA</p>
                            <h4 class="mb-0">{{ number_format($averageGPA, 2) }}</h4>
                        </div>
                        <i class="bi bi-graph-up text-info fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Pending Applications</p>
                            <h4 class="mb-0">{{ number_format($pendingApplications) }}</h4>
                        </div>
                        <i class="bi bi-file-earmark-text text-warning fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Performance --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">This Month's Performance</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <i class="bi bi-people-fill text-primary fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $monthlyNewStudents }}</h3>
                                <p class="text-muted mb-0 small">New Students</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <i class="bi bi-clipboard-check-fill text-success fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $monthlyEnrollments }}</h3>
                                <p class="text-muted mb-0 small">New Enrollments</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <i class="bi bi-currency-dollar text-warning fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $currencyCode }} {{ number_format($monthlyRevenue, 0) }}</h3>
                                <p class="text-muted mb-0 small">Revenue Collected</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Fee Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Collected</span>
                            <span class="fw-bold text-success">{{ $currencyCode }} {{ number_format($collectedRevenue, 0) }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalRevenue > 0 ? ($collectedRevenue / $totalRevenue * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Pending</span>
                            <span class="fw-bold text-warning">{{ $currencyCode }} {{ number_format($pendingRevenue, 0) }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $totalRevenue > 0 ? ($pendingRevenue / $totalRevenue * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="alert alert-danger mb-0 py-2">
                        <small><i class="bi bi-exclamation-triangle"></i> {{ $overdueFeesCount }} overdue records</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="row g-4">
        {{-- Recent Enrollments --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Enrollments</h5>
                        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEnrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->student->name }}</td>
                                    <td>{{ $enrollment->course->name }}</td>
                                    <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No recent enrollments</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Payments</h5>
                        <a href="{{ route('admin.fees.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->feeRecord->student->name }}</td>
                                    <td class="fw-bold text-success">{{ $currencyCode }} {{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($payment->payment_method) }}</span>
                                    </td>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No recent payments</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.students.create') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person-plus me-2"></i>Add Student
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.faculty-staff.create') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-person-badge me-2"></i>Add Faculty
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.courses.create') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-book me-2"></i>Add Course
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
