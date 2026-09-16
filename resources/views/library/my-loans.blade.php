@extends('layouts.app')

@section('title', 'My Borrowing')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">My Borrowing</h2>
            <p class="text-muted mb-0">Your active books, due dates, and borrowing history.</p>
        </div>
        <a href="{{ route('library.index') }}" class="btn btn-outline-secondary">Return to library</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Issued</th>
                            <th>Due date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Software Quality Assurance</td>
                            <td>2026-09-01</td>
                            <td>2026-09-15</td>
                            <td><span class="badge text-bg-warning">Due soon</span></td>
                        </tr>
                        <tr>
                            <td>Foundations of Data Analytics</td>
                            <td>2026-08-22</td>
                            <td>2026-09-08</td>
                            <td><span class="badge text-bg-success">Returned</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
