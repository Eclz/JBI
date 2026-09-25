@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Expense Details</h3>
            <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Category</th>
                    <td>{{ $expense->category }}</td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td>{{ $expense->title }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{ $expense->amount }}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>{{ $expense->expense_date }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
