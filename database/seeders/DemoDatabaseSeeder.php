<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Department;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\Semester;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BasicAcademicSetupSeeder::class,
            CreateTestUsersSeeder::class,
        ]);

        $academicYear = AcademicYear::where('is_current', true)->firstOrFail();
        $semester = Semester::where('academic_year_id', $academicYear->id)
            ->where('is_current', true)
            ->firstOrFail();
        $faculty = User::where('email', 'faculty@jbiuniversity.com')->firstOrFail();
        $student = User::where('email', 'student@jbiuniversity.com')->firstOrFail();
        $admin = User::where('email', 'admin@jbiuniversity.com')->firstOrFail();

        $courses = collect([
            ['code' => 'BIBL101', 'name' => 'Introduction to Biblical Studies', 'department' => 'BIBL'],
            ['code' => 'THEO201', 'name' => 'Systematic Theology', 'department' => 'THEO'],
            ['code' => 'MINI210', 'name' => 'Leadership and Christian Ministry', 'department' => 'MINI'],
        ])->map(function (array $data) use ($faculty, $semester) {
            $department = Department::where('code', $data['department'])->firstOrFail();

            return Course::updateOrCreate(
                ['code' => $data['code']],
                [
                    'course_code' => $data['code'],
                    'name' => $data['name'],
                    'description' => 'Demo course for training and system evaluation.',
                    'credits' => 3,
                    'department_id' => $department->id,
                    'instructor_id' => $faculty->id,
                    'semester_id' => $semester->id,
                    'schedule' => ['monday' => ['start' => '09:00', 'end' => '11:00']],
                    'room' => 'Online Classroom',
                    'max_students' => 30,
                    'status' => 'active',
                    'fee_amount' => 300,
                    'learning_objectives' => 'Develop knowledge, leadership, and practical ministry skills.',
                    'assessment_methods' => 'Assignments, attendance, quizzes, and final examination.',
                ]
            );
        });

        foreach ($courses as $index => $course) {
            CourseEnrollment::updateOrCreate(
                ['user_id' => $student->id, 'course_id' => $course->id],
                [
                    'enrollment_date' => now()->subWeeks(4)->toDateString(),
                    'status' => 'enrolled',
                    'final_grade' => $index === 0 ? 84 : null,
                    'letter_grade' => $index === 0 ? 'A' : null,
                    'grade_points' => $index === 0 ? 4 : null,
                ]
            );

            Assignment::factory()->count(2)->published()->create([
                'course_id' => $course->id,
            ]);

            foreach (range(1, 5) as $daysAgo) {
                Attendance::factory()->create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'attendance_date' => now()->subDays($daysAgo)->toDateString(),
                ]);
            }
        }

        $tuition = FeeStructure::updateOrCreate(
            ['name' => 'Demo Semester Tuition', 'academic_year_id' => $academicYear->id],
            [
                'description' => 'Demonstration tuition charge.',
                'type' => 'tuition',
                'amount' => 300,
                'frequency' => 'semester',
                'semester_id' => $semester->id,
                'applicable_to' => ['student'],
                'is_mandatory' => true,
                'is_active' => true,
                'due_date' => now()->addMonth()->toDateString(),
            ]
        );

        FeeRecord::updateOrCreate(
            ['invoice_number' => 'DEMO-INV-001'],
            [
                'user_id' => $student->id,
                'fee_structure_id' => $tuition->id,
                'amount' => 300,
                'total_amount' => 300,
                'paid_amount' => 150,
                'balance_amount' => 150,
                'status' => 'partial',
                'due_date' => now()->addMonth()->toDateString(),
                'payment_method' => 'bank_transfer',
                'processed_by' => $admin->id,
            ]
        );

        Announcement::factory()->count(3)->create(['created_by' => $admin->id]);

        foreach ([
            'institution_name' => 'JBI University',
            'institution_address' => 'South Africa',
            'institution_phone' => '+27 68 443 8415',
            'institution_email' => 'info@jbiuniversity.com',
            'default_timezone' => 'Africa/Johannesburg',
            'demo_mode' => 'true',
        ] as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'general']
            );
        }

        $this->command?->info('Demo database ready with operator accounts and representative academic data.');
    }
}
