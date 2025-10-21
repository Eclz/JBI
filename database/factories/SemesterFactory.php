<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Semester;
use App\Models\AcademicYear;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester>
 */
class SemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $semesters = [
            [
                'name' => 'Fall',
                'start_month' => 8,
                'end_month' => 12,
                'reg_start_offset' => -45,
                'reg_end_offset' => -5,
            ],
            [
                'name' => 'Spring',
                'start_month' => 1,
                'end_month' => 5,
                'reg_start_offset' => -45,
                'reg_end_offset' => -5,
            ],
            [
                'name' => 'Summer',
                'start_month' => 6,
                'end_month' => 8,
                'reg_start_offset' => -30,
                'reg_end_offset' => -3,
            ],
        ];

        $semester = fake()->randomElement($semesters);
        $academicYear = AcademicYear::inRandomOrder()->first() ?? AcademicYear::factory()->create();

        $year = $semester['name'] === 'Spring' ? $academicYear->year + 1 : $academicYear->year;
        $startDate = "{$year}-{$semester['start_month']}-15";
        $endDate = "{$year}-{$semester['end_month']}-15";

        return [
            'academic_year_id' => $academicYear->id,
            'name' => $semester['name'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_start' => date('Y-m-d', strtotime($startDate . ' ' . $semester['reg_start_offset'] . ' days')),
            'registration_end' => date('Y-m-d', strtotime($startDate . ' ' . $semester['reg_end_offset'] . ' days')),
            'is_current' => false,
            'is_active' => true,
        ];
    }

    /**
     * Create current semester
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => true,
        ]);
    }

    /**
     * Create fall semester
     */
    public function fall(): static
    {
        return $this->state(function (array $attributes) {
            $academicYear = AcademicYear::where('id', $attributes['academic_year_id'])->first();
            $year = $academicYear ? $academicYear->year : 2024;

            return [
                'name' => 'Fall',
                'start_date' => "{$year}-08-15",
                'end_date' => "{$year}-12-15",
                'registration_start' => "{$year}-07-01",
                'registration_end' => "{$year}-08-10",
            ];
        });
    }

    /**
     * Create spring semester
     */
    public function spring(): static
    {
        return $this->state(function (array $attributes) {
            $academicYear = AcademicYear::where('id', $attributes['academic_year_id'])->first();
            $year = $academicYear ? $academicYear->year + 1 : 2025;

            return [
                'name' => 'Spring',
                'start_date' => "{$year}-01-15",
                'end_date' => "{$year}-05-15",
                'registration_start' => ($year - 1) . "-12-01",
                'registration_end' => "{$year}-01-10",
            ];
        });
    }
}
