<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $departments = [
            'Computer Science',
            'Mathematics',
            'Physics',
            'Chemistry',
            'Biology',
            'English Literature',
            'History',
            'Psychology',
            'Economics',
            'Business Administration',
            'Mechanical Engineering',
            'Electrical Engineering',
            'Civil Engineering',
            'Chemical Engineering',
            'Medicine',
            'Nursing',
            'Pharmacy',
            'Law',
            'Education',
            'Fine Arts'
        ];

        $name = $this->faker->unique()->randomElement($departments);

        return [
            'faculty_id' => Faculty::factory(),
            'name' => $name,
            'code' => strtoupper(substr(str_replace(' ', '', $name), 0, 3)) . $this->faker->unique()->numberBetween(100, 999),
            'description' => $this->faker->paragraph(),
            'head_of_department_id' => null, // Will be set after faculty users are created
            'location' => $this->faker->randomElement(['Building A', 'Building B', 'Building C', 'Main Campus', 'North Wing', 'South Wing']) . ' - Floor ' . $this->faker->numberBetween(1, 5),
            'phone' => $this->faker->phoneNumber(),
            'email' => strtolower(str_replace(' ', '', $name)) . '@jbi.edu',
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the department is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the department is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
