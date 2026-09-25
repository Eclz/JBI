@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Asset</h3>
            <a href="{{ route('admin.finance.assets.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.assets.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label>Name</label>
                    <input type="text" name="asset_name" class="form-control" value="{{ $asset->asset_name }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="{{ $asset->category }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Cost</label>
                    <input type="text" name="purchase_cost" class="form-control" value="{{ $asset->purchase_cost }}" required>
                </div>
                <div class="form-group mb-3">
                    <label>Date</label>
                    <input type="text" name="purchase_date" class="form-control" value="{{ $asset->purchase_date }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
