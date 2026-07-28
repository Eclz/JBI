<?php

namespace Database\Factories;

use App\Models\ForumReply;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumReply>
 */
class ForumReplyFactory extends Factory
{
    protected $model = ForumReply::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $topic = ForumTopic::inRandomOrder()->first() ?? ForumTopic::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'content' => $this->faker->paragraph(2),
            'is_approved' => true,
            'likes_count' => $this->faker->numberBetween(0, 25),
        ];
    }
}
