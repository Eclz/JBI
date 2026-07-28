@extends('layouts.guest')

@section('title', 'Upload Payment Proof')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="bi bi-credit-card me-2"></i>Upload Payment Proof</h3>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-success">
                        <h5><i class="bi bi-check-circle me-2"></i>Congratulations! Your application has been approved</h5>
                        <p class="mb-0">Application Number: <strong>{{ $application->application_number }}</strong></p>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Admission Fee Payment Details</h6>
                        <p><strong>Amount to Pay:</strong> 50,000 {{ $currencyCode }}</p>
                        <p class="mb-0"><strong>Bank Details:</strong></p>
                        <ul class="mb-0">
                            <li>Bank Name: Stanbic Bank Uganda</li>
                            <li>Account Name: JBI University</li>
                            <li>Account Number: 9030012345678</li>
                            <li>Branch: Kampala Main Branch</li>
                            <li>Reference: {{ $application->application_number }}</li>
                        </ul>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('applications.store-payment', $application->application_number) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="payment_proof" class="form-label">Upload Payment Receipt <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="payment_proof" name="payment_proof" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Accepted formats: PDF, JPG, PNG. Max size: 5MB</small>
                        </div>

                        <div class="alert alert-warning">
                            <small><i class="bi bi-exclamation-triangle me-2"></i>
                                Please ensure your payment receipt clearly shows:
                                <ul class="mb-0 mt-2">
                                    <li>Transaction date and time</li>
                                    <li>Amount paid (50,000 {{ $currencyCode }})</li>
                                    <li>Reference number: {{ $application->application_number }}</li>
                                    <li>Bank details</li>
                                </ul>
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-upload me-2"></i>Upload Payment Proof
                            </button>
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
