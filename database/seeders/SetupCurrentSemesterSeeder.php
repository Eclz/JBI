<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Semester;

class SetupCurrentSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        // Create current academic year with proper name
        $academicYear = AcademicYear::firstOrCreate(
            ['year' => $currentYear],
            [
                'name' => $currentYear . '-' . $nextYear, // Required name field
                'year' => $currentYear,
                'start_date' => $currentYear . '-01-01',
                'end_date' => $currentYear . '-12-31',
                'is_current' => true,
                'is_active' => true,
            ]
        );

        // Determine current semester based on month
        $currentMonth = date('n');
        if ($currentMonth >= 1 && $currentMonth <= 5) {
            $semesterName = 'Spring';
            $startDate = $currentYear . '-01-15';
            $endDate = $currentYear . '-05-15';
        } elseif ($currentMonth >= 6 && $currentMonth <= 8) {
            $semesterName = 'Summer';
            $startDate = $currentYear . '-06-01';
            $endDate = $currentYear . '-08-15';
        } else {
            $semesterName = 'Fall';
            $startDate = $currentYear . '-08-20';
            $endDate = $currentYear . '-12-15';
        }

        // Set all semesters to not current first
        Semester::query()->update(['is_current' => false]);

        // Create/update current semester
        $semester = Semester::firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'name' => $semesterName,
            ],
            [
                'academic_year_id' => $academicYear->id,
                'name' => $semesterName,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'registration_start' => date('Y-m-d', strtotime($startDate . ' -30 days')),
                'registration_end' => date('Y-m-d', strtotime($startDate . ' -7 days')),
                'is_current' => true,
                'is_active' => true,
            ]
        );

        // Make sure this semester is marked as current
        $semester->update(['is_current' => true]);

        $this->command->info("Academic Year created: {$academicYear->name}");
        $this->command->info("Current semester set: {$semesterName} {$currentYear}");
    }
}
