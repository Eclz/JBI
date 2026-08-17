<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AssignmentSubmission;
use App\Models\Assignment;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssignmentSubmission>
 */
class AssignmentSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assignment = Assignment::inRandomOrder()->first() ?? Assignment::factory()->create();
        $student = User::where('role', 'student')->inRandomOrder()->first() 
                  ?? User::factory()->student()->create();

        $availableFrom = \Carbon\Carbon::parse($assignment->available_from ?? '-30 days');
        $submittedAt = fake()->dateTimeBetween($availableFrom, $availableFrom->copy()->addDays(5));
        $isLate = \Carbon\Carbon::instance($submittedAt)->gt(\Carbon\Carbon::parse($assignment->due_date));
        $daysLate = $isLate ? \Carbon\Carbon::instance($submittedAt)->diffInDays(\Carbon\Carbon::parse($assignment->due_date)) : 0;

        // Generate realistic submission content
        $submissionTexts = [
            "This assignment explores the theological implications of the given passage. Through careful exegesis and historical context analysis, I have examined the key themes and their relevance to contemporary Christian life.",
            "In this reflection paper, I have analyzed the biblical text using the hermeneutical principles discussed in class. The paper includes original language insights and practical applications for ministry.",
            "This research project investigates the historical development of the doctrine under consideration. I have consulted primary sources and contemporary scholarship to present a comprehensive analysis.",
            "The following submission represents my understanding of the assigned reading and its implications for Christian theology and practice. I have included relevant citations and personal reflections.",
        ];

        $files = [];
        if (fake()->boolean(80)) {
            $fileCount = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $fileCount; $i++) {
                $files[] = [
                    'name' => fake()->words(3, true) . '.' . fake()->randomElement(['pdf', 'doc', 'docx']),
                    'path' => 'submissions/' . fake()->uuid() . '.' . fake()->randomElement(['pdf', 'doc', 'docx']),
                    'size' => fake()->numberBetween(100000, 5000000), // bytes
                ];
            }
        }

        return [
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'submission_text' => fake()->randomElement($submissionTexts),
            'submitted_files' => $files,
            'submitted_at' => $submittedAt,
            'is_late' => $isLate,
            'days_late' => $daysLate,
            'score' => fake()->optional(0.7)->randomFloat(2, 0, $assignment->max_points),
            'adjusted_score' => null, // Will be calculated if late
            'feedback' => fake()->optional(0.6)->paragraph(2),
            'rubric_scores' => fake()->optional(0.5)->randomElements([
                'content' => fake()->numberBetween(70, 100),
                'organization' => fake()->numberBetween(70, 100),
                'grammar' => fake()->numberBetween(80, 100),
                'citations' => fake()->numberBetween(75, 100),
            ]),
            'status' => fake()->randomElement(['submitted', 'graded', 'returned']),
            'graded_at' => fake()->optional(0.7)->dateTimeBetween(\Carbon\Carbon::instance($submittedAt), \Carbon\Carbon::instance($submittedAt)->copy()->addDays(3)),
            'graded_by' => $assignment->course->instructor_id,
            'attempt_number' => 1,
        ];
    }

    /**
     * Create graded submission
     */
    public function graded(): static
    {
        return $this->state(function (array $attributes) {
            $assignment = Assignment::find($attributes['assignment_id']);
            $score = fake()->randomFloat(2, $assignment->max_points * 0.6, $assignment->max_points);
            
            return [
                'score' => $score,
                'status' => 'graded',
                'graded_at' => fake()->dateTimeBetween(
                    \Carbon\Carbon::parse($attributes['submitted_at']),
                    \Carbon\Carbon::parse($attributes['submitted_at'])->copy()->addDays(3)
                ),
                'feedback' => fake()->paragraph(2),
            ];
        });
    }

    /**
     * Create late submission
     */
    public function late(): static
    {
        return $this->state(function (array $attributes) {
            $assignment = Assignment::find($attributes['assignment_id']);
            $dueDate = \Carbon\Carbon::parse($assignment->due_date);
            $lateDate = fake()->dateTimeBetween($dueDate, $dueDate->copy()->addDays(7));
            
            return [
                'submitted_at' => $lateDate,
                'is_late' => true,
                'days_late' => $lateDate->diffInDays($dueDate),
            ];
        });
    }
}
