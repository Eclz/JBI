<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use App\Models\Semester;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeStructure>
 */
class FeeStructureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $feeTypes = [
            'tuition' => [
                'names' => ['Undergraduate Tuition', 'Graduate Tuition', 'Seminary Tuition', 'Doctoral Tuition'],
                'amounts' => [8000, 12000, 15000, 18000],
            ],
            'library' => [
                'names' => ['Library Fee', 'Digital Resources Fee', 'Research Access Fee'],
                'amounts' => [200, 150, 100],
            ],
            'laboratory' => [
                'names' => ['Language Lab Fee', 'Computer Lab Fee', 'Media Lab Fee'],
                'amounts' => [300, 250, 200],
            ],
            'technology' => [
                'names' => ['Technology Fee', 'Online Learning Fee', 'Software License Fee'],
                'amounts' => [400, 300, 250],
            ],
            'activity' => [
                'names' => ['Student Activity Fee', 'Chapel Fee', 'Recreation Fee'],
                'amounts' => [150, 100, 75],
            ],
            'other' => [
                'names' => ['Parking Fee', 'Health Services Fee', 'Graduation Fee', 'Transcript Fee'],
                'amounts' => [200, 150, 100, 25],
            ],
        ];

        $type = fake()->randomElement(array_keys($feeTypes));
        $typeData = $feeTypes[$type];
        $nameIndex = fake()->numberBetween(0, count($typeData['names']) - 1);
        $name = $typeData['names'][$nameIndex];
        $baseAmount = $typeData['amounts'][$nameIndex];
        $amount = $baseAmount + fake()->randomFloat(2, -$baseAmount * 0.1, $baseAmount * 0.1);

        $academicYear = AcademicYear::inRandomOrder()->first() ?? AcademicYear::factory()->create();
        $semester = fake()->optional(0.7)->randomElement(Semester::where('academic_year_id', $academicYear->id)->get());

        return [
            'name' => $name,
            'description' => fake()->sentence(),
            'type' => $type,
            'amount' => $amount,
            'frequency' => fake()->randomElement(['one_time', 'semester', 'monthly', 'annual']),
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester?->id,
            'applicable_to' => fake()->randomElements(['student', 'graduate', 'undergraduate', 'seminary'], fake()->numberBetween(1, 2)),
            'is_mandatory' => fake()->boolean(80),
            'is_active' => true,
            'due_date' => fake()->dateTimeBetween('now', '+90 days'),
            'late_fee_amount' => fake()->randomFloat(2, 25, 100),
            'late_fee_days' => fake()->randomElement([7, 14, 30]),
        ];
    }

    /**
     * Create tuition fee
     */
    public function tuition(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'tuition',
            'name' => 'Semester Tuition',
            'amount' => fake()->randomFloat(2, 8000, 15000),
            'is_mandatory' => true,
        ]);
    }

    /**
     * Create mandatory fee
     */
    public function mandatory(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_mandatory' => true,
        ]);
    }
}
