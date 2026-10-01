@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Benefits Management</h2>
            <p class="text-muted mb-0">Manage health, retirement, and other company benefit plans.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                <i class="bi bi-shield-plus me-1"></i> New Plan
            </button>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#enrollEmployeeModal">
                <i class="bi bi-person-plus-fill me-1"></i> Enroll Employee
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Available Plans</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_plans'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-primary fs-4">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Active Enrollments</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['active_enrollments'] }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Monthly Co. Cost (Est.)</h6>
                            <h3 class="mb-0 fw-bold">${{ number_format($stats['monthly_company_cost'], 2) }}</h3>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-warning fs-4">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Benefit Plans -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Benefit Plans</h5>
                </div>
                <div class="card-body p-0">
                    @if($plans->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <p class="mb-2">No benefit plans configured.</p>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">Create First Plan</button>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($plans as $plan)
                                <div class="list-group-item p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-0 fw-bold text-primary">{{ $plan->name }}</h6>
                                            <small class="text-muted">{{ $plan->provider }} • {{ $plan->type }}</small>
                                        </div>
                                        <span class="badge bg-light text-dark border">{{ $plan->enrollments_count }} Enrolled</span>
                                    </div>
                                    <div class="row g-2 mt-2">
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded text-center">
                                                <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 700;">Employee Cost</small>
                                                <span class="fw-bold">${{ number_format($plan->employee_cost, 2) }}<small>/mo</small></span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded text-center">
                                                <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 700;">Company Cost</small>
                                                <span class="fw-bold">${{ number_format($plan->company_cost, 2) }}<small>/mo</small></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Employee Enrollments -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Employee Enrollments</h5>
                </div>
                <div class="card-body p-0">
                    @if($enrollments->isEmpty())
                        <div class="p-5 text-center text-muted flex-grow-1">
                            <i class="bi bi-file-medical fs-1 mb-3 d-block opacity-50"></i>
                            <h6>No employees enrolled in benefits.</h6>
                            <p class="small">Enroll an employee to see their active coverage.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4 border-0">Employee</th>
                                        <th class="py-3 px-4 border-0">Plan & Provider</th>
                                        <th class="py-3 px-4 border-0">Enrolled On</th>
                                        <th class="py-3 px-4 border-0">Status</th>
                                        <th class="py-3 px-4 border-0 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $enrollment->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($enrollment->user->name).'&background=random' }}" alt="{{ $enrollment->user->name }}" class="rounded-circle me-3" style="width: 35px; height: 35px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0">{{ $enrollment->user->name }}</h6>
                                                        <small class="text-muted">{{ $enrollment->user->hrProfile->department ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="fw-semibold text-primary">{{ $enrollment->plan->name }}</div>
                                                <div class="small text-muted">{{ $enrollment->plan->provider }}</div>
                                            </td>
                                            <td class="px-4 text-muted small">
                                                {{ $enrollment->enrollment_date->format('d M Y') }}
                                            </td>
                                            <td class="px-4">
                                                @if($enrollment->status === 'Active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Terminated</span>
                                                @endif
                                            </td>
                                            <td class="px-4 text-end">
                                                @if($enrollment->status === 'Active')
                                                    <form action="{{ route('human-resources.benefits.terminate', $enrollment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to terminate this benefit enrollment?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Terminate Enrollment">
                                                            <i class="bi bi-x-circle"></i> Terminate
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.benefits.plans.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Benefit Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Gold Health Coverage">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provider Name</label>
                        <input type="text" name="provider" class="form-control" required placeholder="e.g. BlueCross">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Benefit Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Health">Health Insurance</option>
                            <option value="Dental">Dental Insurance</option>
                            <option value="Vision">Vision Insurance</option>
                            <option value="Retirement">Retirement / 401(k)</option>
                            <option value="Life Insurance">Life Insurance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Employee Cost/Mo ($)</label>
                            <input type="number" name="employee_cost" class="form-control" required step="0.01" min="0" value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Company Cost/Mo ($)</label>
                            <input type="number" name="company_cost" class="form-control" required step="0.01" min="0" value="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description / Coverage Details</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of coverage..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enroll Employee Modal -->
<div class="modal fade" id="enrollEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.benefits.enroll') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Enroll Employee in Benefits</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->hrProfile->department ?? 'General' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Benefit Plan</label>
                        <select name="hr_benefit_plan_id" class="form-select" required>
                            <option value="">-- Select Plan --</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enrollment Start Date</label>
                        <input type="date" name="enrollment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Complete Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
