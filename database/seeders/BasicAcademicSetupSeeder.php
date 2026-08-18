<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Department;

class BasicAcademicSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create departments first
        $departments = [
            ['name' => 'Biblical Studies', 'code' => 'BIBL', 'description' => 'Biblical Studies Department'],
            ['name' => 'Theology', 'code' => 'THEO', 'description' => 'Theology Department'],
            ['name' => 'Christian Ministry', 'code' => 'MINI', 'description' => 'Christian Ministry Department'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }

        $this->call(FacultySeeder::class);

        // Create academic year
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        $academicYear = AcademicYear::firstOrCreate(
            ['year' => $currentYear],
            [
                'name' => $currentYear . '-' . $nextYear,
                'year' => $currentYear,
                'start_date' => $currentYear . '-08-01',
                'end_date' => $nextYear . '-07-31',
                'is_current' => true,
                'is_active' => true,
            ]
        );

        // Create semesters
        $semesters = [
            [
                'name' => 'Fall',
                'start_date' => $currentYear . '-08-15',
                'end_date' => $currentYear . '-12-15',
                'registration_start' => $currentYear . '-07-01',
                'registration_end' => $currentYear . '-08-10',
                'is_current' => true,
            ],
            [
                'name' => 'Spring',
                'start_date' => $nextYear . '-01-15',
                'end_date' => $nextYear . '-05-15',
                'registration_start' => $currentYear . '-11-01',
                'registration_end' => $nextYear . '-01-10',
                'is_current' => false,
            ],
            [
                'name' => 'Summer',
                'start_date' => $nextYear . '-06-01',
                'end_date' => $nextYear . '-07-31',
                'registration_start' => $nextYear . '-04-01',
                'registration_end' => $nextYear . '-05-25',
                'is_current' => false,
            ],
        ];

        foreach ($semesters as $semesterData) {
            Semester::firstOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'name' => $semesterData['name'],
                ],
                array_merge($semesterData, [
                    'academic_year_id' => $academicYear->id,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Basic academic setup completed successfully!');
        $this->command->info("Academic Year: {$academicYear->name}");
        $this->command->info('Departments: ' . Department::count() . ' created');
        $this->command->info('Semesters: ' . Semester::count() . ' created');
    }
}
