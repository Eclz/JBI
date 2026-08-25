<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\SystemSetting;
use App\Models\StudentProfile;
use App\Models\FacultyProfile;
use App\Models\FeeStructure;
use App\Models\FeeRecord;
use App\Models\Announcement;
use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\CourseMaterial;
use App\Models\Notification;
use App\Models\AuditLog;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Set timezone to SAST for consistency with current time (01:06 PM SAST, May 28, 2025)
        date_default_timezone_set('Africa/Johannesburg');

        // Create Academic Years
        $currentAcademicYear = AcademicYear::factory()->create([
            'name' => '2024-2025',
            'year' => 2024,
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
            'is_current' => true,
        ]);

        $previousAcademicYear = AcademicYear::factory()->create([
            'name' => '2023-2024',
            'year' => 2023,
            'start_date' => '2023-08-15',
            'end_date' => '2024-05-15',
            'is_current' => false,
        ]);

        // Create Semesters for current academic year
        $fallSemester = Semester::factory()->fall()->create([
            'academic_year_id' => $currentAcademicYear->id,
        ]);

        $springSemester = Semester::factory()->spring()->create([
            'academic_year_id' => $currentAcademicYear->id,
        ]);

        // Create Departments (avoid duplicates by checking existing codes)
        $departments = [
            ['name' => 'Biblical Studies', 'code' => 'BIBL'],
            ['name' => 'Theology', 'code' => 'THEO'],
            ['name' => 'Church History', 'code' => 'HIST'],
            ['name' => 'Christian Ministry', 'code' => 'MINI'],
            ['name' => 'Biblical Languages', 'code' => 'LANG'],
        ];

        $createdDepartments = collect();
        foreach ($departments as $dept) {
            $existing = Department::where('code', $dept['code'])->first();
            if (!$existing) {
                $createdDepartments->push(Department::factory()->create($dept));
            } else {
                $createdDepartments->push($existing);
            }
        }

        // Seed Roles and Permissions
        $this->call(RolePermissionSeeder::class);

        // Seed Faculties and link departments
        $this->call(FacultySeeder::class);

        // Create Admin Users
        User::factory()->admin()->create([
            'name' => 'System Administrator',
            'email' => 'admin@jbiuniversity.com',
            'employee_id' => 'JBI001',
            'preferences' => json_encode(['theme' => 'dark', 'notifications' => true]),
        ]);

        User::factory()->admin()->create([
            'name' => 'Academic Administrator',
            'email' => 'academic@jbiuniversity.com',
            'employee_id' => 'JBI002',
            'preferences' => json_encode(['theme' => 'light', 'notifications' => true]),
        ]);

        // Create Faculty Users with Profiles
        $facultyUsers = User::factory()->faculty()->count(25)->create();

        foreach ($facultyUsers as $faculty) {
            FacultyProfile::factory()->create([
                'user_id' => $faculty->id,
                'department_id' => $createdDepartments->random()->id,
            ]);
        }

        // Assign department heads
        foreach ($createdDepartments as $department) {
            $departmentFaculty = FacultyProfile::where('department_id', $department->id)->first();
            if ($departmentFaculty) {
                $department->update(['head_of_department_id' => $departmentFaculty->user_id]);
            }
        }

        // Create Student Users with Profiles
        $studentUsers = User::factory()->student()->count(150)->create();

        foreach ($studentUsers as $student) {
            StudentProfile::factory()->create([
                'user_id' => $student->id,
                'department_id' => $createdDepartments->random()->id,
            ]);
        }

        // Create some graduated students
        $graduatedStudents = User::factory()->student()->count(20)->create();
        foreach ($graduatedStudents as $student) {
            StudentProfile::factory()->graduated()->create([
                'user_id' => $student->id,
                'department_id' => $createdDepartments->random()->id,
            ]);
        }

        // Create Courses
        $courses = collect();
        foreach ($createdDepartments as $department) {
            $departmentFaculty = User::whereHas('facultyProfile', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })->get();

            if ($departmentFaculty->isNotEmpty()) {
                $coursesForDept = Course::factory()->count(8)->create([
                    'department_id' => $department->id,
                    'semester_id' => $fallSemester->id,
                    'instructor_id' => $departmentFaculty->random()->id,
                ]);
                $courses = $courses->merge($coursesForDept);
            }
        }

        // Enroll students in courses
        foreach ($studentUsers->take(100) as $student) {
            $studentCourses = $courses->random(fake()->numberBetween(3, 6));
            foreach ($studentCourses as $course) {
                $student->enrolledCourses()->attach($course->id, [
                    'enrollment_date' => fake()->dateTimeBetween('-60 days', '-30 days'),
                    'status' => 'enrolled',
                ]);
            }
        }

        // Create Assignments for each course
        foreach ($courses as $course) {
            Assignment::factory()->count(fake()->numberBetween(3, 8))->create([
                'course_id' => $course->id,
            ]);
        }

        // Create Assignment Submissions and Grades
        foreach ($courses as $course) {
            $enrolledStudents = $course->students;
            $assignments = $course->assignments;

            foreach ($assignments as $assignment) {
                foreach ($enrolledStudents as $student) {
                    if (fake()->boolean(85)) { // 85% submission rate
                        \App\Models\AssignmentSubmission::factory()->create([
                            'assignment_id' => $assignment->id,
                            'user_id' => $student->id,
                        ]);
                    }
                }
            }
        }

        // Create Attendance Records
        foreach ($courses as $course) {
            $enrolledStudents = $course->students;

            // Create attendance for the last 30 days
            for ($i = 30; $i >= 0; $i--) {
                $attendanceDate = now()->subDays($i);

                // Skip weekends
                if ($attendanceDate->isWeekend()) continue;

                foreach ($enrolledStudents as $student) {
                    \App\Models\Attendance::factory()->create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'attendance_date' => $attendanceDate->format('Y-m-d'),
                    ]);
                }
            }
        }

        // Create Fee Structures
        $feeStructures = collect();

        // Tuition fees
        $feeStructures->push(FeeStructure::factory()->tuition()->create([
            'academic_year_id' => $currentAcademicYear->id,
            'semester_id' => $fallSemester->id,
            'name' => 'Fall Semester Tuition',
            'amount' => 12000,
        ]));

        $feeStructures->push(FeeStructure::factory()->tuition()->create([
            'academic_year_id' => $currentAcademicYear->id,
            'semester_id' => $springSemester->id,
            'name' => 'Spring Semester Tuition',
            'amount' => 12000,
        ]));

        // Other fees
        $otherFees = [
            ['name' => 'Library Fee', 'type' => 'library', 'amount' => 200],
            ['name' => 'Technology Fee', 'type' => 'technology', 'amount' => 400],
            ['name' => 'Student Activity Fee', 'type' => 'activity', 'amount' => 150],
            ['name' => 'Parking Fee', 'type' => 'other', 'amount' => 100],
        ];

        foreach ($otherFees as $fee) {
            $feeStructures->push(FeeStructure::factory()->mandatory()->create([
                'academic_year_id' => $currentAcademicYear->id,
                'name' => $fee['name'],
                'type' => $fee['type'],
                'amount' => $fee['amount'],
            ]));
        }

        // Create Fee Records for students
        foreach ($studentUsers->take(120) as $student) {
            foreach ($feeStructures as $feeStructure) {
                FeeRecord::factory()->create([
                    'user_id' => $student->id,
                    'fee_structure_id' => $feeStructure->id,
                    'amount' => $feeStructure->amount,
                ]);
            }
        }

        // Create some paid fee records
        \App\Models\FeeRecord::factory()->paid()->count(200)->create();

        // Create some overdue fee records
        \App\Models\FeeRecord::factory()->overdue()->count(50)->create();

        // Create Announcements
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            Announcement::factory()->count(15)->create([
                'created_by' => $adminUser->id,
            ]);
        }

        // Create course-specific announcements
        foreach ($courses->take(10) as $course) {
            Announcement::factory()->count(2)->create([
                'course_id' => $course->id,
                'created_by' => $course->instructor_id,
            ]);
        }

        // Create System Settings
        $settings = [
            ['key' => 'institution_name', 'value' => 'JBI University', 'group' => 'general'],
            ['key' => 'institution_address', 'value' => 'South Africa', 'group' => 'general'],
            ['key' => 'institution_phone', 'value' => '+27 68 443 8415', 'group' => 'general'],
            ['key' => 'institution_email', 'value' => 'info@jbiuniversity.com', 'group' => 'general'],
            ['key' => 'academic_year_start_month', 'value' => '8', 'group' => 'academic'],
            ['key' => 'default_semester_credits', 'value' => '15', 'group' => 'academic'],
            ['key' => 'max_course_enrollment', 'value' => '30', 'group' => 'academic'],
            ['key' => 'late_fee_percentage', 'value' => '5', 'group' => 'financial'],
            ['key' => 'payment_gateway', 'value' => 'stripe', 'group' => 'financial'],
            ['key' => 'email_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'sms_notifications', 'value' => 'false', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'default_timezone', 'value' => 'Africa/Johannesburg', 'group' => 'general'],
            ['key' => 'academic_calendar_url', 'value' => 'https://jbiuniversity.com/', 'group' => 'academic'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::create($setting);
        }

        // Create Forums
        Forum::factory()->count(8)->create();

        // Create Forum Topics and Replies
        $forums = Forum::all();
        foreach ($forums as $forum) {
            $topics = ForumTopic::factory()->count(fake()->numberBetween(3, 8))->create([
                'forum_id' => $forum->id,
                'user_id' => User::inRandomOrder()->first()->id,
            ]);

            foreach ($topics as $topic) {
                ForumReply::factory()->count(fake()->numberBetween(0, 5))->create([
                    'topic_id' => $topic->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                ]);
            }
        }

        // Create Course Materials
        foreach ($courses as $course) {
            CourseMaterial::factory()->count(fake()->numberBetween(2, 6))->create([
                'course_id' => $course->id,
                'uploaded_by' => $course->instructor_id,
            ]);
        }

        // Create Notifications for users
        foreach (User::take(50)->get() as $user) {
            Notification::factory()->count(fake()->numberBetween(1, 5))->create([
                'user_id' => $user->id,
            ]);
        }

        // Create some audit logs
        AuditLog::factory()->count(100)->create();

        // Output summary
        $this->command->info('Database seeded successfully with realistic JBI University data!');
        $this->command->info('Created:');
        $this->command->info('- ' . User::count() . ' users');
        $this->command->info('- ' . Department::count() . ' departments');
        $this->command->info('- ' . Course::count() . ' courses');
        $this->command->info('- ' . Assignment::count() . ' assignments');
        $this->command->info('- ' . \App\Models\AssignmentSubmission::count() . ' assignment submissions');
        $this->command->info('- ' . \App\Models\Attendance::count() . ' attendance records');
        $this->command->info('- ' . \App\Models\FeeRecord::count() . ' fee records');
        $this->command->info('- ' . Announcement::count() . ' announcements');
    }
}
