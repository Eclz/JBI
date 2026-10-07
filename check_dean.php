<?php
// Check faculties and their dean assignments
$faculties = \App\Models\Faculty::select('id', 'name', 'dean_id')->get();
echo "=== FACULTIES ===\n";
foreach ($faculties as $f) {
    echo "ID: {$f->id}, Name: {$f->name}, Dean ID: {$f->dean_id}\n";
}

// Check departments
$depts = \App\Models\Department::select('id', 'name', 'faculty_id')->take(10)->get();
echo "\n=== DEPARTMENTS ===\n";
foreach ($depts as $d) {
    echo "ID: {$d->id}, Name: {$d->name}, Faculty ID: {$d->faculty_id}\n";
}

// Check currently logged in dean user (id=4 from previous sessions)
$user = \App\Models\User::find(4);
if ($user) {
    echo "\n=== USER #4 ===\n";
    echo "Name: {$user->name}, Role: {$user->role}\n";
    
    // Check if user is dean of any faculty
    $deanFaculties = \App\Models\Faculty::where('dean_id', 4)->get();
    echo "Dean of faculties: " . $deanFaculties->pluck('name')->join(', ') . "\n";
}

// Check student profiles with department_id
$studentProfilesWithDept = \App\Models\StudentProfile::whereNotNull('department_id')->count();
echo "\n=== STUDENT PROFILES WITH DEPARTMENT: {$studentProfilesWithDept} ===\n";

// Check faculty profiles with department_id
$facultyProfilesWithDept = \App\Models\FacultyProfile::whereNotNull('department_id')->count();
echo "=== FACULTY PROFILES WITH DEPARTMENT: {$facultyProfilesWithDept} ===\n";

// Check system settings for currency
$currency = \App\Models\SystemSetting::where('key', 'default_currency')->first();
echo "\n=== CURRENCY: " . ($currency ? $currency->value : 'NOT SET') . " ===\n";

// Check programs with department
$programsWithDept = \App\Models\Program::whereNotNull('department_id')->count();
echo "=== PROGRAMS WITH DEPARTMENT: {$programsWithDept} ===\n";
