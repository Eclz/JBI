@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Budget</h3>
            <a href="{{ route('admin.finance.budgets.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.budgets.update', $budget->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label>Academic Year</label>
                    <input type="text" name="academic_year" class="form-control" value="{{ $budget->academic_year }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Allocated Amount</label>
                    <input type="text" name="allocated_amount" class="form-control" value="{{ $budget->allocated_amount }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection