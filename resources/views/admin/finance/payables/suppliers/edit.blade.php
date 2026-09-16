@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Supplier</h3>
            <a href="{{ route('admin.finance.payables.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.payables.suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3"><label>Company Name</label><input type="text" name="company_name" class="form-control" value="{{ $supplier->company_name }}" required></div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
