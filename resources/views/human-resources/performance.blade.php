@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Performance Management</h2>
            <p class="text-muted mb-0">Track employee goals and manage periodic performance reviews.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#assignGoalModal">
                <i class="bi bi-bullseye me-1"></i> Assign Goal
            </button>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#initiateReviewModal">
                <i class="bi bi-star me-1"></i> Initiate Review
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Active Goals</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['active_goals'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-primary fs-4">
                            <i class="bi bi-bullseye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Goals Completed</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['completed_goals'] }}</h3>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Pending Reviews</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['pending_reviews'] }}</h3>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-warning fs-4">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Company Avg Rating</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['avg_rating'] }} <small class="text-muted fs-6">/ 5</small></h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Performance Reviews Ledger -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Performance Reviews</h5>
                </div>
                <div class="card-body p-0">
                    @if($reviews->isEmpty())
                        <div class="p-5 text-center text-muted flex-grow-1">
                            <i class="bi bi-star fs-1 mb-3 d-block opacity-50"></i>
                            <h6>No reviews initiated.</h6>
                            <p class="small">Start a performance cycle to evaluate staff.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4 border-0">Employee</th>
                                        <th class="py-3 px-4 border-0">Period</th>
                                        <th class="py-3 px-4 border-0">Status</th>
                                        <th class="py-3 px-4 border-0">Rating</th>
                                        <th class="py-3 px-4 border-0 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $review->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($review->user->name).'&background=random' }}" alt="{{ $review->user->name }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0" style="font-size: 0.9rem;">{{ $review->user->name }}</h6>
                                                        <small class="text-muted">{{ $review->user->hrProfile->department ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 text-muted small fw-semibold">
                                                {{ $review->review_period }}
                                            </td>
                                            <td class="px-4">
                                                @if($review->status === 'Draft')
                                                    <span class="badge bg-light text-dark border">Draft</span>
                                                @elseif($review->status === 'Scheduled')
                                                    <span class="badge bg-warning text-dark">Scheduled</span>
                                                @else
                                                    <span class="badge bg-success">Completed</span>
                                                @endif
                                            </td>
                                            <td class="px-4">
                                                @if($review->overall_rating)
                                                    <div class="text-warning">
                                                        @for($i=1; $i<=5; $i++)
                                                            @if($i <= $review->overall_rating)
                                                                <i class="bi bi-star-fill"></i>
                                                            @else
                                                                <i class="bi bi-star text-muted opacity-25"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                @else
                                                    <span class="text-muted small fst-italic">Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-4 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editReviewModal{{ $review->id }}">
                                                    Manage
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Edit Review Modal -->
                                        <div class="modal fade" id="editReviewModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('human-resources.performance.reviews.update', $review->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Review: {{ $review->user->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Review Status</label>
                                                                <select name="status" class="form-select">
                                                                    <option value="Draft" {{ $review->status == 'Draft' ? 'selected' : '' }}>Draft (Preparing)</option>
                                                                    <option value="Scheduled" {{ $review->status == 'Scheduled' ? 'selected' : '' }}>Scheduled / In Progress</option>
                                                                    <option value="Completed" {{ $review->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Overall Rating (1-5)</label>
                                                                <input type="number" name="overall_rating" class="form-control" min="1" max="5" value="{{ $review->overall_rating }}" {{ $review->status == 'Completed' ? '' : 'placeholder="Leave blank until completed"' }}>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Reviewer Comments / Summary</label>
                                                                <textarea name="comments" class="form-control" rows="4" placeholder="Feedback...">{{ $review->comments }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save Updates</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Goals Ledger -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Active Goals</h5>
                </div>
                <div class="card-body p-0">
                    @if($goals->isEmpty())
                        <div class="p-5 text-center text-muted flex-grow-1">
                            <i class="bi bi-bullseye fs-1 mb-3 d-block opacity-50"></i>
                            <h6>No goals set.</h6>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($goals as $goal)
                                <div class="list-group-item p-4 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $goal->title }}</h6>
                                            <div class="small text-muted mb-2">
                                                Assigned to: <span class="fw-semibold">{{ $goal->user->name }}</span>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#updateGoalModal{{ $goal->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-1 small text-muted">
                                        <span>Progress: {{ $goal->progress_percentage }}%</span>
                                        <span class="{{ $goal->due_date < now() && $goal->status != 'Completed' ? 'text-danger fw-bold' : '' }}">
                                            Due: {{ $goal->due_date->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $goal->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $goal->progress_percentage }}%;"></div>
                                    </div>
                                </div>

                                <!-- Update Goal Modal -->
                                <div class="modal fade" id="updateGoalModal{{ $goal->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('human-resources.performance.goals.update', $goal->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Goal Progress</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6 class="mb-3">{{ $goal->title }}</h6>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="Not Started" {{ $goal->status == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                                            <option value="In Progress" {{ $goal->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="Completed" {{ $goal->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="Cancelled" {{ $goal->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Progress (%)</label>
                                                        <input type="range" class="form-range" name="progress_percentage" min="0" max="100" step="5" value="{{ $goal->progress_percentage }}" oninput="this.nextElementSibling.value = this.value + '%'">
                                                        <output class="d-block text-center fw-bold mt-2">{{ $goal->progress_percentage }}%</output>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Progress</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Initiate Review Modal -->
<div class="modal fade" id="initiateReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.performance.reviews.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Initiate Performance Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee to Review</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->hrProfile->department ?? 'General' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reviewer / Manager</label>
                        <select name="reviewer_id" class="form-select" required>
                            <option value="{{ auth()->id() }}" selected>Me ({{ auth()->user()->name }})</option>
                            @foreach($users as $user)
                                @if($user->id !== auth()->id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Review Period</label>
                            <input type="text" name="review_period" class="form-control" required placeholder="e.g. Q3 2026 or Annual 2026">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Scheduled Date (Optional)</label>
                            <input type="date" name="review_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Draft Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Goal Modal -->
<div class="modal fade" id="assignGoalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.performance.goals.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign New Goal</h5>
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
                        <label class="form-label">Goal Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Increase sales by 15%">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description & Metrics</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="How will success be measured?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Goal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
