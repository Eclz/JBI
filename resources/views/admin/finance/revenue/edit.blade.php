@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Revenue</h3>
            <a href="{{ route('admin.finance.revenue.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.revenue.update', $revenue->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="{{ $revenue->category }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $revenue->title }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Amount</label>
                    <input type="text" name="amount" class="form-control" value="{{ $revenue->amount }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Date</label>
                    <input type="text" name="transaction_date" class="form-control" value="{{ $revenue->transaction_date }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Payer</label>
                    <input type="text" name="payer_name" class="form-control" value="{{ $revenue->payer_name }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
