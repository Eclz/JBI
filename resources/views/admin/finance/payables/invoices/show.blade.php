@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Vendor Invoice Details</h3>
            <a href="{{ route('admin.finance.payables.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Invoice Number</th><td>{{ $invoice->invoice_number }}</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection
