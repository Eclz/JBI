<?php

namespace Database\Factories;

use App\Models\ForumTopic;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumTopic>
 */
class ForumTopicFactory extends Factory
{
    protected $model = ForumTopic::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $forum = Forum::inRandomOrder()->first() ?? Forum::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $topics = [
            'How to study for Systematic Theology?',
            'Discussion on Biblical Exegesis of Romans 5',
            'Welcome to the JBI Seminary Forum!',
            'Recommendation for Hebrew dictionaries',
            'Reformation History reading list',
            'Worship leadership best practices',
            'Youth ministry outreach ideas',
            'Questions about graduation requirements',
        ];

        return [
            'forum_id' => $forum->id,
            'title' => $this->faker->randomElement($topics) . ' ' . $this->faker->numberBetween(1, 100),
            'content' => $this->faker->paragraph(4),
            'user_id' => $user->id,
            'is_pinned' => $this->faker->boolean(10), // 10% chance pinned
            'is_locked' => $this->faker->boolean(5),  // 5% chance locked
            'is_approved' => true,
            'views_count' => $this->faker->numberBetween(10, 200),
            'replies_count' => 0,
            'tags' => $this->faker->words(3),
        ];
    }
}
