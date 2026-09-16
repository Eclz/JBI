@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Bank Account</h3>
            <a href="{{ route('admin.finance.banking.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.banking.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ $account->bank_name }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control" value="{{ $account->account_number }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Account Name</label>
                    <input type="text" name="account_name" class="form-control" value="{{ $account->account_name }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Balance</label>
                    <input type="text" name="current_balance" class="form-control" value="{{ $account->current_balance }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection