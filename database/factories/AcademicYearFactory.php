<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AcademicYear;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->numberBetween(2020, 2025);
        $startDate = "{$year}-08-15";
        $endDate = ($year + 1) . "-05-15";

        return [
            'name' => "{$year}-" . ($year + 1),
            'year' => $year,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_current' => $year == 2024, // Make 2024-2025 current
            'is_active' => true,
        ];
    }

    /**
     * Create current academic year
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => true,
            'year' => 2024,
            'name' => '2024-2025',
            'start_date' => '2024-08-15',
            'end_date' => '2025-05-15',
        ]);
    }
}
