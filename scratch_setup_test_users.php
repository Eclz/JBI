<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = ['head_of_department', 'dean', 'registrar', 'finance_officer'];
foreach($roles as $roleSlug) {
    $role = App\Models\Role::where('slug', $roleSlug)->first();
    if($role) {
        $user = App\Models\User::where('role_id', $role->id)->first();
        if(!$user) {
            $user = App\Models\User::create([
                'name' => ucfirst(str_replace('_', ' ', $roleSlug)) . ' Test',
                'email' => $roleSlug . '@example.com',
                'password' => bcrypt('password'),
                'role_id' => $role->id,
                'role' => $role->guard_role,
            ]);
            echo "Created user for ".$roleSlug." with email ".$user->email."\n";
        } else {
            $user->password = bcrypt('password');
            $user->save();
            echo "Found user for ".$roleSlug." with email ".$user->email."\n";
        }
    }
}
