@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Leave Requests</h2>
            <p class="text-muted mb-0">Track staff leave, approvals, and balances.</p>
        </div>
        <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New request</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sarah Bassey</td>
                            <td>Annual leave</td>
                            <td>2026-10-01</td>
                            <td>2026-10-05</td>
                            <td><span class="badge text-bg-warning">Pending</span></td>
                        </tr>
                        <tr>
                            <td>Daniel Akpan</td>
                            <td>Sick leave</td>
                            <td>2026-09-10</td>
                            <td>2026-09-12</td>
                            <td><span class="badge text-bg-success">Approved</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
