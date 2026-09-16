@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Supplier Details</h3>
            <a href="{{ route('admin.finance.payables.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Company Name</th><td>{{ $supplier->company_name }}</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection