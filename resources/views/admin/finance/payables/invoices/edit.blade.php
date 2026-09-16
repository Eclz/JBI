@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Edit Vendor Invoice</h3>
            <a href="{{ route('admin.finance.payables.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.payables.invoices.update', $invoice->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3"><label>Invoice Number</label><input type="text" name="invoice_number" class="form-control" value="{{ $invoice->invoice_number }}" required></div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
