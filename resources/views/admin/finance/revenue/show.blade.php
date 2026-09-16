@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Revenue Details</h3>
            <a href="{{ route('admin.finance.revenue.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Category</th>
                    <td>{{ $revenue->category }}</td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td>{{ $revenue->title }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{ $revenue->amount }}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>{{ $revenue->transaction_date }}</td>
                </tr>
                <tr>
                    <th>Payer</th>
                    <td>{{ $revenue->payer_name }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection