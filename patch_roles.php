<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = \App\Models\Role::find(3);
if ($role) {
    $permissions = $role->permissions;
    $permissions['academic_quality'] = ['view' => true, 'create' => true, 'edit' => true, 'approve' => false, 'export' => true];
    $permissions['faculty_evaluations'] = ['view' => true, 'create' => true, 'edit' => true, 'approve' => false, 'export' => true];
    $permissions['budget_requests'] = ['view' => true, 'create' => true, 'edit' => true, 'approve' => false, 'export' => true];
    $permissions['student_support'] = ['view' => true, 'create' => true, 'edit' => true, 'approve' => true, 'export' => true];
    $permissions['external_relations'] = ['view' => true, 'create' => true, 'edit' => true, 'approve' => false, 'export' => true];

    $role->permissions = $permissions;
    $role->save();
    echo "Permissions updated for role 3 (Dean) with new modules.\n";
} else {
    echo "Role 3 not found\n";
}
