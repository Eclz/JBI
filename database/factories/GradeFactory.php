<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Grade;
use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
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
        $course = Course::inRandomOrder()->first() ?? Course::factory()->create();
        $assignment = Assignment::where('course_id', $course->id)->inRandomOrder()->first() 
                     ?? Assignment::factory()->create(['course_id' => $course->id]);

        $gradeTypes = ['assignment', 'quiz', 'exam', 'participation', 'project', 'final'];
        $gradeType = fake()->randomElement($gradeTypes);

        $pointsPossible = match($gradeType) {
            'quiz' => fake()->randomElement([10, 15, 20, 25]),
            'assignment' => fake()->randomElement([50, 75, 100]),
            'participation' => fake()->randomElement([25, 50]),
            'project' => fake()->randomElement([100, 150, 200]),
            'exam' => fake()->randomElement([150, 200, 250]),
            'final' => fake()->randomElement([200, 250, 300]),
            default => 100,
        };

        $pointsEarned = fake()->randomFloat(2, $pointsPossible * 0.5, $pointsPossible);
        $percentage = round(($pointsEarned / $pointsPossible) * 100, 2);

        // Calculate letter grade
        $letterGrade = match(true) {
            $percentage >= 97 => 'A+',
            $percentage >= 93 => 'A',
            $percentage >= 90 => 'A-',
            $percentage >= 87 => 'B+',
            $percentage >= 83 => 'B',
            $percentage >= 80 => 'B-',
            $percentage >= 77 => 'C+',
            $percentage >= 73 => 'C',
            $percentage >= 70 => 'C-',
            $percentage >= 67 => 'D+',
            $percentage >= 63 => 'D',
            $percentage >= 60 => 'D-',
            default => 'F',
        };

        // Calculate grade points for GPA
        $gradePoints = match($letterGrade) {
            'A+', 'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.7,
            'D+' => 1.3,
            'D' => 1.0,
            'D-' => 0.7,
            default => 0.0,
        };

        return [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'assignment_id' => $gradeType === 'assignment' ? $assignment->id : null,
            'grade_type' => $gradeType,
            'points_earned' => $pointsEarned,
            'points_possible' => $pointsPossible,
            'percentage' => $percentage,
            'letter_grade' => $letterGrade,
            'grade_points' => $gradePoints,
            'comments' => fake()->optional(0.6)->sentence(),
            'is_published' => fake()->boolean(85),
            'graded_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'graded_by' => $course->instructor_id,
        ];
    }

    /**
     * Create published grade
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Create grade of specific type
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'grade_type' => $type,
        ]);
    }

    /**
     * Create high grade (A range)
     */
    public function high(): static
    {
        return $this->state(function (array $attributes) {
            $pointsEarned = fake()->randomFloat(2, $attributes['points_possible'] * 0.9, $attributes['points_possible']);
            $percentage = round(($pointsEarned / $attributes['points_possible']) * 100, 2);
            
            return [
                'points_earned' => $pointsEarned,
                'percentage' => $percentage,
                'letter_grade' => fake()->randomElement(['A+', 'A', 'A-']),
                'grade_points' => fake()->randomFloat(1, 3.7, 4.0),
            ];
        });
    }
}
