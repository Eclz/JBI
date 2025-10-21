<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Department;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first() 
                  ?? User::factory()->student()->create();
        $department = Department::inRandomOrder()->first() ?? Department::factory()->create();

        $programs = [
            'Bachelor of Arts in Biblical Studies',
            'Bachelor of Arts in Theology',
            'Bachelor of Arts in Christian Ministry',
            'Master of Divinity',
            'Master of Arts in Biblical Studies',
            'Master of Arts in Theology',
            'Doctor of Ministry',
            'Certificate in Biblical Studies',
        ];

        $specializations = [
            'Biblical Exegesis',
            'Systematic Theology',
            'Pastoral Ministry',
            'Youth Ministry',
            'Missions',
            'Church History',
            'Christian Education',
            'Worship Leadership',
            'Biblical Languages',
            'Apologetics',
        ];

        $currentSemester = fake()->numberBetween(1, 8);
        $totalCreditsRequired = match(true) {
            str_contains($programs[0], 'Bachelor') => 120,
            str_contains($programs[0], 'Master') => 60,
            str_contains($programs[0], 'Doctor') => 90,
            default => 30,
        };

        $totalCreditsEarned = fake()->numberBetween(0, min($totalCreditsRequired, $currentSemester * 15));
        $currentGpa = fake()->randomFloat(2, 2.0, 4.0);
        $cumulativeGpa = fake()->randomFloat(2, max(2.0, $currentGpa - 0.3), min(4.0, $currentGpa + 0.2));

        return [
            'user_id' => $student->id,
            'admission_number' => 'ADM' . fake()->unique()->numerify('######'),
            'admission_date' => fake()->dateTimeBetween('-4 years', '-1 month'),
            'department_id' => $department->id,
            'program' => fake()->randomElement($programs),
            'specialization' => fake()->optional(0.7)->randomElement($specializations),
            'current_semester' => $currentSemester,
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive', 'graduated', 'dropped']),
            'current_gpa' => $currentGpa,
            'cumulative_gpa' => $cumulativeGpa,
            'total_credits_earned' => $totalCreditsEarned,
            'total_credits_required' => $totalCreditsRequired,
            'expected_graduation_date' => fake()->dateTimeBetween('+6 months', '+4 years'),
            'actual_graduation_date' => fake()->optional(0.1)->dateTimeBetween('-2 years', 'now'),
            'guardian_name' => fake()->name(),
            'guardian_phone' => fake()->phoneNumber(),
            'guardian_email' => fake()->email(),
            'guardian_address' => fake()->address(),
            'previous_school' => fake()->optional(0.8)->company() . ' High School',
            'academic_history' => [
                [
                    'institution' => fake()->company() . ' High School',
                    'graduation_year' => fake()->year('-10 years'),
                    'gpa' => fake()->randomFloat(2, 2.5, 4.0),
                ],
            ],
            'achievements' => fake()->optional(0.6)->randomElements([
                'Dean\'s List',
                'Academic Excellence Award',
                'Perfect Attendance',
                'Student Leadership Award',
                'Community Service Recognition',
                'Biblical Languages Proficiency',
            ], fake()->numberBetween(1, 3)),
            'notes' => fake()->optional(0.3)->paragraph(),
        ];
    }

    /**
     * Create active student profile
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Create graduated student profile
     */
    public function graduated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'graduated',
            'actual_graduation_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'total_credits_earned' => $attributes['total_credits_required'],
        ]);
    }
}
