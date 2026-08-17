<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $actions = ['create', 'update', 'delete', 'login', 'logout'];
        $action = $this->faker->randomElement($actions);

        $models = [
            'App\Models\Course',
            'App\Models\User',
            'App\Models\Assignment',
            'App\Models\StudentProfile',
            'App\Models\Department',
        ];
        $modelType = $this->faker->randomElement($models);

        return [
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => in_array($action, ['login', 'logout']) ? null : $modelType,
            'model_id' => in_array($action, ['login', 'logout']) ? null : $this->faker->numberBetween(1, 100),
            'old_values' => $action === 'update' ? ['status' => 'inactive'] : null,
            'new_values' => $action === 'update' ? ['status' => 'active'] : ($action === 'create' ? ['name' => $this->faker->word()] : null),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'description' => 'User ' . $user->name . ' performed action ' . $action,
        ];
    }
}
