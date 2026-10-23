<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Duplicate submission protection
    |--------------------------------------------------------------------------
    |
    | Every state-changing web request (POST, PUT, PATCH, DELETE) is guarded by
    | App\Http\Middleware\EnsureIdempotentSubmission. A duplicate request (same
    | user, route and payload) waits for the original to finish and receives the
    | original result instead of executing the action a second time.
    |
    */

    'enabled' => env('IDEMPOTENCY_ENABLED', true),

    // Hidden form field added to every form by public/js/form-guard.js.
    'input_key' => '_idempotency_key',

    // Header that AJAX callers may send to opt into key-based replay.
    'header' => 'X-Idempotency-Key',

    // How long a completed keyed submission is remembered (back button / re-submit).
    'key_ttl' => (int) env('IDEMPOTENCY_KEY_TTL', 3600),

    // How long an unkeyed request is remembered after completion (double clicks).
    'fingerprint_ttl' => (int) env('IDEMPOTENCY_FINGERPRINT_TTL', 3),

    // Maximum lifetime of an in-flight lock (protects against crashed workers).
    'lock_ttl' => 120,

    // How long a duplicate waits for the original request before giving up.
    'wait_seconds' => 10,

    // Routes that must never be deduplicated (auth flows rotate the session/CSRF token).
    'except_routes' => [
        'login',
        'logout',
        'register',
        'password.*',
        'verification.*',
    ],

    'except_paths' => [
        'livewire/*',
        'broadcasting/*',
        '_debugbar/*',
    ],
];
