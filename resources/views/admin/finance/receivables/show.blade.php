@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Receivable Details</h3>
            <a href="{{ route('admin.finance.receivables.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Student</th><td>{{ $record->student->name ?? 'N/A' }}</td></tr>
                <tr><th>Balance</th><td>{{ $record->balance_amount }}</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection
