<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\FacultyProfile;
use App\Models\Department;

class CreateTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create departments first if they don't exist
        $departments = [
            ['name' => 'Biblical Studies', 'code' => 'BIBL', 'description' => 'Study of Biblical texts and interpretation'],
            ['name' => 'Theology', 'code' => 'THEO', 'description' => 'Systematic study of Christian doctrine'],
            ['name' => 'Church History', 'code' => 'HIST', 'description' => 'History of Christianity and the Church'],
            ['name' => 'Christian Ministry', 'code' => 'MINI', 'description' => 'Practical ministry and pastoral care'],
            ['name' => 'Biblical Languages', 'code' => 'LANG', 'description' => 'Hebrew, Greek, and Aramaic languages'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }

        $firstDepartment = Department::first();

        // Delete existing test users to avoid conflicts
        User::whereIn('email', [
            'admin@jbiuniversity.com',
            'faculty@jbiuniversity.com',
            'student@jbiuniversity.com'
        ])->delete();

        // Create Admin User
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@jbiuniversity.com',
            'password' => Hash::make('password123'), // Explicitly hash the password
            'role' => User::ROLE_ADMIN,
            'employee_id' => 'JBI001',
            'is_active' => true,
            'phone' => '+1-555-0101',
            'address' => '123 Admin Street, JBI Campus',
        ]);

        $this->command->info('✓ Admin user created: admin@jbiuniversity.com');

        // Create Faculty User
        $faculty = User::create([
            'name' => 'Dr. John Faculty',
            'email' => 'faculty@jbiuniversity.com',
            'password' => Hash::make('password123'), // Explicitly hash the password
            'role' => User::ROLE_FACULTY,
            'employee_id' => 'JBI002',
            'is_active' => true,
            'phone' => '+1-555-0102',
            'address' => '456 Faculty Lane, JBI Campus',
            'date_of_birth' => '1975-05-15',
            'gender' => 'male',
        ]);

        // Create Faculty Profile
        FacultyProfile::create([
            'user_id' => $faculty->id,
            'department_id' => $firstDepartment->id,
            'position' => 'Professor',
            'hire_date' => now()->subYears(5),
            'employment_status' => 'active',
            'office_location' => 'Faculty Building, Room 201',
            'office_hours' => 'Monday-Friday 2:00-4:00 PM',
        ]);

        $this->command->info('✓ Faculty user created: faculty@jbiuniversity.com');

        // Create Student User
        $student = User::create([
            'name' => 'Jane Student',
            'email' => 'student@jbiuniversity.com',
            'password' => Hash::make('password123'), // Explicitly hash the password
            'role' => User::ROLE_STUDENT,
            'student_id' => 'JBI2024001',
            'is_active' => true,
            'phone' => '+1-555-0103',
            'address' => '789 Student Drive, JBI Campus',
            'date_of_birth' => '2000-08-20',
            'gender' => 'female',
            'emergency_contact' => 'Mary Student (Mother)',
            'emergency_phone' => '+1-555-0104',
        ]);

        // Create Student Profile
        StudentProfile::create([
            'user_id' => $student->id,
            'department_id' => $firstDepartment->id,
            'program' => 'Bachelor of Theology',
            'admission_date' => now()->subMonths(6),
            'academic_status' => 'active',
            'year_level' => 1,
            'gpa' => 3.75,
        ]);

        $this->command->info('✓ Student user created: student@jbiuniversity.com');

        // Create additional test users for different roles
        $parent = User::create([
            'name' => 'Robert Parent',
            'email' => 'parent@jbiuniversity.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_PARENT,
            'is_active' => true,
            'phone' => '+1-555-0105',
            'address' => '321 Parent Avenue, Hometown',
        ]);

        $this->command->info('✓ Parent user created: parent@jbiuniversity.com');

        $this->command->info('');
        $this->command->info('🎉 Test users created successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('==================');
        $this->command->info('Admin:   admin@jbiuniversity.com   / password123');
        $this->command->info('Faculty: faculty@jbiuniversity.com / password123');
        $this->command->info('Student: student@jbiuniversity.com / password123');
        $this->command->info('Parent:  parent@jbiuniversity.com  / password123');
        $this->command->info('');
        $this->command->info('All passwords are properly hashed with bcrypt.');
    }
}
