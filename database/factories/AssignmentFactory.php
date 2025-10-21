<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Assignment;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assignmentTypes = [
            'homework' => [
                'Reading Assignment Chapter 1-3',
                'Biblical Text Analysis',
                'Theological Reflection Paper',
                'Scripture Memorization',
                'Discussion Questions Response',
            ],
            'essay' => [
                'Exegetical Paper on Romans 8',
                'Theological Position Paper',
                'Church History Research Essay',
                'Comparative Religion Analysis',
                'Personal Ministry Philosophy',
            ],
            'project' => [
                'Sermon Preparation Project',
                'Ministry Plan Development',
                'Biblical Timeline Creation',
                'Church Visit and Report',
                'Community Service Project',
            ],
            'quiz' => [
                'Weekly Scripture Quiz',
                'Vocabulary Quiz - Biblical Terms',
                'Historical Dates Quiz',
                'Theological Concepts Quiz',
                'Language Translation Quiz',
            ],
            'exam' => [
                'Midterm Examination',
                'Final Examination',
                'Comprehensive Bible Knowledge Test',
                'Theological Systems Exam',
                'Practical Ministry Assessment',
            ],
        ];

        $type = fake()->randomElement(array_keys($assignmentTypes));
        $title = fake()->randomElement($assignmentTypes[$type]);
        
        $course = Course::inRandomOrder()->first() ?? Course::factory()->create();
        
        // Generate realistic due dates
        $dueDate = fake()->dateTimeBetween('now', '+60 days');
        $availableFrom = fake()->dateTimeBetween('-30 days', $dueDate);
        $availableUntil = fake()->optional(0.7)->dateTimeBetween($dueDate, '+7 days');

        $maxPoints = match($type) {
            'quiz' => fake()->randomElement([10, 15, 20, 25]),
            'homework' => fake()->randomElement([25, 50, 75]),
            'essay' => fake()->randomElement([100, 150, 200]),
            'project' => fake()->randomElement([150, 200, 250]),
            'exam' => fake()->randomElement([200, 250, 300]),
            default => 100,
        };

        return [
            'course_id' => $course->id,
            'title' => $title,
            'description' => fake()->paragraph(2),
            'instructions' => fake()->paragraphs(3, true),
            'type' => $type,
            'max_points' => $maxPoints,
            'weight_percentage' => fake()->randomFloat(2, 5, 25),
            'due_date' => $dueDate,
            'available_from' => $availableFrom,
            'available_until' => $availableUntil,
            'allow_late_submission' => fake()->boolean(70),
            'late_penalty_per_day' => fake()->randomElement([0, 5, 10, 15]),
            'allowed_file_types' => fake()->randomElements(['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx'], fake()->numberBetween(1, 3)),
            'max_file_size' => fake()->randomElement([5120, 10240, 20480]), // KB
            'is_published' => fake()->boolean(80),
            'rubric' => fake()->optional(0.6)->paragraph(2),
            'settings' => [
                'auto_grade' => fake()->boolean(20),
                'show_correct_answers' => fake()->boolean(60),
                'randomize_questions' => fake()->boolean(30),
                'time_limit' => fake()->optional(0.4)->numberBetween(30, 180), // minutes
            ],
        ];
    }

    /**
     * Create published assignment
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Create assignment of specific type
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Create overdue assignment
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'is_published' => true,
        ]);
    }
}
