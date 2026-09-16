@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Payroll Record Details</h3>
            <a href="{{ route('admin.finance.payroll.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Month/Year</th><td>{{ $payroll->month_year }}</td></tr>
                <tr><th>Net Salary</th><td>{{ $payroll->net_salary }}</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection
