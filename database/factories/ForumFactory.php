<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Forum;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Forum>
 */
class ForumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $forumNames = [
            'General Discussion',
            'Academic Questions',
            'Student Life',
            'Prayer Requests',
            'Study Groups',
            'Ministry Opportunities',
            'Technical Support',
            'Course Materials',
            'Biblical Studies Discussion',
            'Theology Debates',
        ];

        $type = fake()->randomElement(['course', 'department', 'general']);
        
        return [
            'name' => fake()->randomElement($forumNames),
            'description' => fake()->paragraph(),
            'course_id' => $type === 'course' ? Course::inRandomOrder()->first()?->id : null,
            'department_id' => $type === 'department' ? Department::inRandomOrder()->first()?->id : null,
            'type' => $type,
            'access_roles' => fake()->optional(0.7)->randomElements(['student', 'faculty', 'admin'], fake()->numberBetween(1, 3)),
            'is_active' => true,
            'allow_anonymous' => fake()->boolean(30),
            'moderated' => fake()->boolean(60),
            'created_by' => User::whereIn('role', ['admin', 'faculty'])->inRandomOrder()->first()?->id 
                           ?? User::factory()->admin()->create()->id,
        ];
    }
}
