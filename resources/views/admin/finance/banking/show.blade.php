@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Bank Account Details</h3>
            <a href="{{ route('admin.finance.banking.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Bank Name</th>
                    <td>{{ $account->bank_name }}</td>
                </tr>
                <tr>
                    <th>Account Number</th>
                    <td>{{ $account->account_number }}</td>
                </tr>
                <tr>
                    <th>Account Name</th>
                    <td>{{ $account->account_name }}</td>
                </tr>
                <tr>
                    <th>Balance</th>
                    <td>{{ $account->current_balance }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
