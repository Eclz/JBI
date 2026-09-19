@extends('layouts.app')

@section('title', 'Time Tracking & Attendance')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Time & Attendance</h2>
            <p class="text-muted mb-0">Track staff working hours, shifts, and timesheets.</p>
        </div>
        <button class="btn btn-primary"><i class="bi bi-clock me-2"></i>Log Hours</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Total Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ date('M d, Y') }}</td>
                            <td>Sarah Bassey</td>
                            <td>08:00 AM</td>
                            <td>05:00 PM</td>
                            <td>9.0</td>
                            <td><span class="badge bg-success">Approved</span></td>
                        </tr>
                        <tr>
                            <td>{{ date('M d, Y') }}</td>
                            <td>Daniel Akpan</td>
                            <td>09:15 AM</td>
                            <td>—</td>
                            <td>—</td>
                            <td><span class="badge bg-warning text-dark">Active Shift</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
