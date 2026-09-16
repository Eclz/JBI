@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Grant</h3>
            <a href="{{ route('admin.finance.grants.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.grants.update', $grant->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label>Title</label>
                    <input type="text" name="project_title" class="form-control" value="{{ $grant->project_title }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Donor</label>
                    <input type="text" name="donor_organization" class="form-control" value="{{ $grant->donor_organization }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Amount</label>
                    <input type="text" name="total_grant_amount" class="form-control" value="{{ $grant->total_grant_amount }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Start</label>
                    <input type="text" name="start_date" class="form-control" value="{{ $grant->start_date }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>End</label>
                    <input type="text" name="end_date" class="form-control" value="{{ $grant->end_date }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection