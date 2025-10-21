<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(['student', 'faculty', 'admin']),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'emergency_contact' => fake()->name(),
            'emergency_phone' => fake()->phoneNumber(),
            'is_active' => true,
            'preferences' => [
                'notifications' => [
                    'email' => fake()->boolean(80),
                    'sms' => fake()->boolean(60),
                    'push' => fake()->boolean(90),
                ],
                'language' => 'en',
                'timezone' => fake()->timezone(),
            ],
            'last_login_at' => fake()->optional(0.8)->dateTimeBetween('-30 days', 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an admin user
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
            'employee_id' => 'JBI' . fake()->unique()->numberBetween(1000, 1999),
            'name' => fake()->name() . ' (Admin)',
        ]);
    }

    /**
     * Create a faculty user
     */
    public function faculty(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_FACULTY,
            'employee_id' => 'JBI' . fake()->unique()->numberBetween(2000, 2999),
            'name' => fake()->randomElement(['Dr.', 'Prof.', 'Rev.']) . ' ' . fake()->name(),
        ]);
    }

    /**
     * Create a student user
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_STUDENT,
            'student_id' => 'JBI' . fake()->unique()->numberBetween(3000, 9999),
            'date_of_birth' => fake()->dateTimeBetween('-25 years', '-17 years')->format('Y-m-d'),
        ]);
    }

    /**
     * Create a parent user
     */
    public function parent(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_PARENT,
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-35 years')->format('Y-m-d'),
        ]);
    }

    /**
     * Create an inactive user
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
