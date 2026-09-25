@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Grant Details</h3>
            <a href="{{ route('admin.finance.grants.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Title</th>
                    <td>{{ $grant->project_title }}</td>
                </tr>
                <tr>
                    <th>Donor</th>
                    <td>{{ $grant->donor_organization }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{ $grant->total_grant_amount }}</td>
                </tr>
                <tr>
                    <th>Start</th>
                    <td>{{ $grant->start_date }}</td>
                </tr>
                <tr>
                    <th>End</th>
                    <td>{{ $grant->end_date }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
