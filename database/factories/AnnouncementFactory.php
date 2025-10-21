<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['general', 'academic', 'administrative', 'event'];
        $type = fake()->randomElement($types);

        $titles = [
            'general' => [
                'Welcome to the New Academic Year',
                'Campus Safety Reminder',
                'Library Hours Update',
                'Student Parking Information',
                'Campus Events This Week',
            ],
            'academic' => [
                'Registration Deadline Approaching',
                'Final Exam Schedule Released',
                'Academic Calendar Updates',
                'Course Add/Drop Period',
                'Graduation Requirements Reminder',
            ],
            'administrative' => [
                'Tuition Payment Due Date',
                'Financial Aid Information',
                'Student Records Update',
                'Policy Changes Notification',
                'Administrative Office Hours',
            ],
            'event' => [
                'Chapel Service Schedule',
                'Guest Speaker Event',
                'Student Ministry Fair',
                'Academic Conference',
                'Community Service Opportunity',
            ],
        ];

        $title = fake()->randomElement($titles[$type]);
        
        $creator = User::whereIn('role', ['admin', 'faculty'])->inRandomOrder()->first() 
                  ?? User::factory()->admin()->create();

        return [
            'title' => $title,
            'content' => fake()->paragraphs(3, true),
            'type' => $type,
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'course_id' => fake()->optional(0.3)->randomElement(Course::pluck('id')->toArray()),
            'department_id' => fake()->optional(0.2)->randomElement(Department::pluck('id')->toArray()),
            'target_roles' => fake()->optional(0.7)->randomElements(['student', 'faculty', 'admin'], fake()->numberBetween(1, 2)),
            'is_published' => fake()->boolean(85),
            'send_email' => fake()->boolean(60),
            'send_sms' => fake()->boolean(20),
            'published_at' => fake()->optional(0.85)->dateTimeBetween('-30 days', 'now'),
            'expires_at' => fake()->optional(0.4)->dateTimeBetween('now', '+60 days'),
            'created_by' => $creator->id,
        ];
    }

    /**
     * Create published announcement
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create urgent announcement
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
            'send_email' => true,
            'is_published' => true,
        ]);
    }
}
