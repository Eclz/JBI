@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Asset Details</h3>
            <a href="{{ route('admin.finance.assets.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <td>{{ $asset->asset_name }}</td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td>{{ $asset->category }}</td>
                </tr>
                <tr>
                    <th>Cost</th>
                    <td>{{ $asset->purchase_cost }}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>{{ $asset->purchase_date }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection