@extends('layouts.app')

@section('title', 'Loans & Returns')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Loans & Returns</h2>
            <p class="text-muted mb-0">Issue, renew, return, and manage circulation records.</p>
        </div>
        <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New loan</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Title</th>
                            <th>Issued</th>
                            <th>Due date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mary Abiola</td>
                            <td>Public Policy and Governance</td>
                            <td>2026-09-05</td>
                            <td>2026-09-19</td>
                            <td><span class="badge text-bg-warning">Due soon</span></td>
                            <td><button class="btn btn-sm btn-outline-primary">Renew</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
