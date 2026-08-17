<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $types = ['assignment_due', 'grade_posted', 'announcement', 'system_alert'];
        $type = $this->faker->randomElement($types);

        return [
            'user_id' => $user->id,
            'type' => $type,
            'title' => 'New notification for ' . ucfirst(str_replace('_', ' ', $type)),
            'message' => $this->faker->sentence(10),
            'data' => null,
            'priority' => $this->faker->randomElement(['low', 'normal', 'high']),
            'is_read' => $this->faker->boolean(30), // 30% chance read
        ];
    }
}
