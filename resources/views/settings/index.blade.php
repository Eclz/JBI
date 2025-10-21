@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-gear me-2"></i>Settings
        </h1>
        <p class="text-muted mb-0">Manage your account preferences</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Settings</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Settings functionality will be implemented here.</p>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Email Notifications</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label" for="emailNotifications">
                                Receive email notifications
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Language</label>
                        <select class="form-select">
                            <option selected>English</option>
                            <option>Spanish</option>
                            <option>French</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Time Zone</label>
                        <select class="form-select">
                            <option selected>UTC-5 (Eastern Time)</option>
                            <option>UTC-6 (Central Time)</option>
                            <option>UTC-7 (Mountain Time)</option>
                            <option>UTC-8 (Pacific Time)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-outline-secondary ms-2">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person me-2"></i>Edit Profile
                    </a>
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </button>
                    <button type="button" class="btn btn-outline-info">
                        <i class="bi bi-download me-2"></i>Export Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
