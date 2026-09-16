@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Expense</h3>
            <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.expenses.update', $expense->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="{{ $expense->category }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $expense->title }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Amount</label>
                    <input type="text" name="amount" class="form-control" value="{{ $expense->amount }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Date</label>
                    <input type="text" name="expense_date" class="form-control" value="{{ $expense->expense_date }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection