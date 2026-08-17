<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faculty>
 */
class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faculties = [
            'Faculty of Computer Science & Information Technology' => 'FCS',
            'Faculty of Engineering' => 'FEN',
            'Faculty of Science' => 'FSC',
            'Faculty of Business & Economics' => 'FBE',
            'Faculty of Arts & Humanities' => 'FAH',
            'Faculty of Medicine & Health Sciences' => 'FMH',
            'Faculty of Law' => 'FLW',
            'Faculty of Education' => 'FED',
        ];

        $name = $this->faker->unique()->randomElement(array_keys($faculties));
        $code = $faculties[$name];

        return [
            'name' => $name,
            'code' => $code,
            'description' => $this->faker->paragraph(),
            'dean_id' => null, // Will be set or remains null
            'location' => $this->faker->randomElement(['Building A', 'Building B', 'Building C', 'Main Campus', 'North Wing', 'South Wing']) . ' - Floor ' . $this->faker->numberBetween(1, 5),
            'phone' => $this->faker->phoneNumber(),
            'email' => strtolower($code) . '@jbi.edu',
            'website' => 'https://' . strtolower($code) . '.jbi.edu',
            'is_active' => true,
        ];
    }
}
