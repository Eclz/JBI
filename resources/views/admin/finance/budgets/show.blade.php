@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Budget Details</h3>
            <a href="{{ route('admin.finance.budgets.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Academic Year</th>
                    <td>{{ $budget->academic_year }}</td>
                </tr>
                <tr>
                    <th>Allocated Amount</th>
                    <td>{{ $budget->allocated_amount }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
