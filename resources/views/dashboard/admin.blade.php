@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-primary text-white shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold mb-1">Welcome back, {{ auth()->user()->first_name }}!</h2>
                            <p class="mb-0">Here's what's happening at JBI University today.</p>
                        </div>
                        <div class="d-none d-md-block">
                            <div class="text-end">
                                <h5 class="mb-1">{{ now()->format('l, F j, Y') }}</h5>
                                <p class="mb-0">Academic Year: {{ $currentAcademicYear ?? '2023-2024' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Students</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalStudents ?? 1250 }}</h2>
                            <p class="text-success mb-0 small">
                                <i class="bi bi-arrow-up"></i> 5.3% from last semester
                            </p>
                        </div>
                        <div class="icon-box bg-primary-subtle text-primary rounded p-3">
                            <i class="bi bi-mortarboard fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Faculty Members</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalFaculty ?? 85 }}</h2>
                            <p class="text-success mb-0 small">
                                <i class="bi bi-arrow-up"></i> 2.1% from last year
                            </p>
                        </div>
                        <div class="icon-box bg-success-subtle text-success rounded p-3">
                            <i class="bi bi-person-badge fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active Courses</h6>
                            <h2 class="mb-0 fw-bold">{{ $activeCourses ?? 142 }}</h2>
                            <p class="text-success mb-0 small">
                                <i class="bi bi-arrow-up"></i> 8.7% from last semester
                            </p>
                        </div>
                        <div class="icon-box bg-info-subtle text-info rounded p-3">
                            <i class="bi bi-journal-bookmark fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Revenue (Monthly)</h6>
                            <h2 class="mb-0 fw-bold">${{ number_format($monthlyRevenue ?? 425000) }}</h2>
                            <p class="text-danger mb-0 small">
                                <i class="bi bi-arrow-down"></i> 2.4% from last month
                            </p>
                        </div>
                        <div class="icon-box bg-warning-subtle text-warning rounded p-3">
                            <i class="bi bi-cash-stack fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending Fees</h6>
                            <h2 class="mb-0 fw-bold">${{ number_format($pendingFees ?? 185000) }}</h2>
                            <p class="text-danger mb-0 small">
                                <i class="bi bi-exclamation-triangle"></i> 32 students with overdue payments
                            </p>
                        </div>
                        <div class="icon-box bg-danger-subtle text-danger rounded p-3">
                            <i class="bi bi-credit-card fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Departments</h6>
                            <h2 class="mb-0 fw-bold">{{ $departments ?? 12 }}</h2>
                            <p class="text-primary mb-0 small">
                                <i class="bi bi-plus-circle"></i> 1 new department this year
                            </p>
                        </div>
                        <div class="icon-box bg-secondary-subtle text-secondary rounded p-3">
                            <i class="bi bi-building fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Attendance Rate</h6>
                            <h2 class="mb-0 fw-bold">{{ $attendanceRate ?? '87' }}%</h2>
                            <p class="text-success mb-0 small">
                                <i class="bi bi-arrow-up"></i> 3.2% from last semester
                            </p>
                        </div>
                        <div class="icon-box bg-success-subtle text-success rounded p-3">
                            <i class="bi bi-calendar-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Graduation Rate</h6>
                            <h2 class="mb-0 fw-bold">{{ $graduationRate ?? '92' }}%</h2>
                            <p class="text-success mb-0 small">
                                <i class="bi bi-arrow-up"></i> 1.8% from last year
                            </p>
                        </div>
                        <div class="icon-box bg-primary-subtle text-primary rounded p-3">
                            <i class="bi bi-award fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Enrollment Trends Chart -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Enrollment & Revenue Trends</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            This Year
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                            <li><a class="dropdown-item" href="#">Last Year</a></li>
                            <li><a class="dropdown-item" href="#">Last 3 Years</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="enrollmentRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Department Performance -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">Department Performance</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Computer Science</span>
                            <span class="text-primary">320 students</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 85%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Business Administration</span>
                            <span class="text-success">285 students</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Engineering</span>
                            <span class="text-info">240 students</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 65%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Medicine</span>
                            <span class="text-warning">180 students</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Arts & Humanities</span>
                            <span class="text-danger">150 students</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Fee Collection Analytics -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">Fee Collection Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <canvas id="feeCollectionChart" height="200"></canvas>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                <div class="color-dot bg-success me-2"></div>
                                <span>Paid</span>
                            </div>
                            <h5 class="mb-0">68%</h5>
                        </div>
                        <div class="col-4">
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                <div class="color-dot bg-warning me-2"></div>
                                <span>Partial</span>
                            </div>
                            <h5 class="mb-0">22%</h5>
                        </div>
                        <div class="col-4">
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                <div class="color-dot bg-danger me-2"></div>
                                <span>Unpaid</span>
                            </div>
                            <h5 class="mb-0">10%</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activities</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle">
                                        <i class="bi bi-person-plus"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">New student registered</h6>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                    <p class="text-muted mb-0 small">John Smith registered as a new student in Computer Science department</p>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-success-subtle text-success rounded-circle">
                                        <i class="bi bi-cash"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Fee payment received</h6>
                                        <small class="text-muted">5 hours ago</small>
                                    </div>
                                    <p class="text-muted mb-0 small">Sarah Johnson paid $2,500 for Fall 2023 semester fees</p>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-warning-subtle text-warning rounded-circle">
                                        <i class="bi bi-journal-plus"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">New course added</h6>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                    <p class="text-muted mb-0 small">Prof. Williams added a new course: "Advanced Machine Learning"</p>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-info-subtle text-info rounded-circle">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Event scheduled</h6>
                                        <small class="text-muted">2 days ago</small>
                                    </div>
                                    <p class="text-muted mb-0 small">Annual Tech Symposium scheduled for November 15-17, 2023</p>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-danger-subtle text-danger rounded-circle">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">System maintenance</h6>
                                        <small class="text-muted">3 days ago</small>
                                    </div>
                                    <p class="text-muted mb-0 small">System maintenance completed. Database optimized and backups created.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">Quick Access</h5>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 quick-access-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-primary-subtle text-primary rounded-circle mx-auto mb-3">
                        <i class="bi bi-person-plus fs-1"></i>
                    </div>
                    <h5>Add New Student</h5>
                    <p class="text-muted mb-3">Register a new student in the system</p>
                    <a href="#" class="btn btn-sm btn-primary">Get Started</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 quick-access-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-success-subtle text-success rounded-circle mx-auto mb-3">
                        <i class="bi bi-journal-plus fs-1"></i>
                    </div>
                    <h5>Create Course</h5>
                    <p class="text-muted mb-3">Add a new course to the curriculum</p>
                    <a href="#" class="btn btn-sm btn-success">Get Started</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 quick-access-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-info-subtle text-info rounded-circle mx-auto mb-3">
                        <i class="bi bi-file-earmark-bar-graph fs-1"></i>
                    </div>
                    <h5>Generate Reports</h5>
                    <p class="text-muted mb-3">Create custom academic reports</p>
                    <a href="#" class="btn btn-sm btn-info">Get Started</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 quick-access-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-warning-subtle text-warning rounded-circle mx-auto mb-3">
                        <i class="bi bi-calendar-plus fs-1"></i>
                    </div>
                    <h5>Schedule Events</h5>
                    <p class="text-muted mb-3">Plan and organize university events</p>
                    <a href="#" class="btn btn-sm btn-warning">Get Started</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-access-card {
    transition: transform 0.2s ease-in-out;
}

.quick-access-card:hover {
    transform: translateY(-5px);
}

.list-group-item {
    transition: background-color 0.2s ease-in-out;
}

.list-group-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enrollment & Revenue Chart
        const enrollmentRevenueCtx = document.getElementById('enrollmentRevenueChart').getContext('2d');
        const enrollmentRevenueChart = new Chart(enrollmentRevenueCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Enrollment',
                        data: [120, 115, 110, 105, 100, 95, 200, 250, 210, 190, 180, 170],
                        backgroundColor: 'rgba(13, 110, 253, 0.5)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                        order: 2
                    },
                    {
                        label: 'Revenue ($1000)',
                        data: [300, 290, 280, 270, 260, 250, 500, 600, 550, 500, 480, 450],
                        type: 'line',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        pointBackgroundColor: 'rgba(25, 135, 84, 1)',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end'
                    }
                }
            }
        });

        // Fee Collection Chart
        const feeCollectionCtx = document.getElementById('feeCollectionChart').getContext('2d');
        const feeCollectionChart = new Chart(feeCollectionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Partial', 'Unpaid'],
                datasets: [{
                    data: [68, 22, 10],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(25, 135, 84, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
