<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FacultyProfile;
use App\Models\User;
use App\Models\Department;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FacultyProfile>
 */
class FacultyProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faculty = User::where('role', 'faculty')->inRandomOrder()->first() 
                  ?? User::factory()->faculty()->create();
        $department = Department::inRandomOrder()->first() ?? Department::factory()->create();

        $designations = [
            'Professor',
            'Associate Professor',
            'Assistant Professor',
            'Adjunct Professor',
            'Lecturer',
            'Senior Lecturer',
            'Visiting Professor',
            'Professor Emeritus',
        ];

        $qualifications = [
            'Ph.D. in Biblical Studies',
            'Ph.D. in Theology',
            'Ph.D. in Church History',
            'Th.D. in Systematic Theology',
            'D.Min. in Pastoral Ministry',
            'M.Div., Ph.D. in New Testament',
            'M.A., Ph.D. in Old Testament',
            'M.Th., Ph.D. in Historical Theology',
        ];

        $specializations = [
            'Old Testament Exegesis',
            'New Testament Studies',
            'Systematic Theology',
            'Church History',
            'Pastoral Theology',
            'Biblical Languages',
            'Christian Ethics',
            'Homiletics',
            'Missiology',
            'Apologetics',
        ];

        $yearsOfExperience = fake()->numberBetween(2, 40);
        $salary = fake()->randomFloat(2, 45000, 120000);

        return [
            'user_id' => $faculty->id,
            'department_id' => $department->id,
            'designation' => fake()->randomElement($designations),
            'qualification' => fake()->randomElement($qualifications),
            'specialization' => fake()->randomElement($specializations),
            'joining_date' => fake()->dateTimeBetween('-20 years', '-1 year'),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'visiting']),
            'salary' => $salary,
            'office_location' => fake()->randomElement([
                'Johnson Hall 301',
                'Johnson Hall 302',
                'Academic Building A-201',
                'Academic Building B-105',
                'Seminary Building 201',
                'Faculty Wing 101',
            ]),
            'office_hours' => fake()->randomElement([
                'Monday & Wednesday 2:00-4:00 PM',
                'Tuesday & Thursday 10:00 AM-12:00 PM',
                'Monday, Wednesday, Friday 1:00-2:00 PM',
                'By appointment',
                'Tuesday 9:00 AM-12:00 PM, Thursday 2:00-4:00 PM',
            ]),
            'research_interests' => fake()->paragraph(2),
            'publications' => fake()->optional(0.8)->randomElements([
                [
                    'title' => 'Exegetical Studies in Romans',
                    'journal' => 'Journal of Biblical Studies',
                    'year' => fake()->year('-10 years'),
                ],
                [
                    'title' => 'Theological Perspectives on Ministry',
                    'publisher' => 'Academic Press',
                    'year' => fake()->year('-5 years'),
                ],
                [
                    'title' => 'Commentary on Ephesians',
                    'publisher' => 'Seminary Publications',
                    'year' => fake()->year('-3 years'),
                ],
            ], fake()->numberBetween(1, 3)),
            'certifications' => fake()->optional(0.6)->randomElements([
                'Certified Biblical Counselor',
                'Licensed Minister',
                'Ordained Pastor',
                'Certified Chaplain',
                'Biblical Languages Certificate',
            ], fake()->numberBetween(1, 3)),
            'years_of_experience' => $yearsOfExperience,
            'bio' => fake()->paragraphs(3, true),
            'linkedin_profile' => fake()->optional(0.5)->url(),
            'personal_website' => fake()->optional(0.3)->url(),
            'status' => fake()->randomElement(['active', 'active', 'active', 'on_leave', 'retired']),
        ];
    }

    /**
     * Create active faculty profile
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Create senior faculty profile
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'designation' => fake()->randomElement(['Professor', 'Associate Professor']),
            'years_of_experience' => fake()->numberBetween(15, 40),
            'salary' => fake()->randomFloat(2, 80000, 120000),
        ]);
    }
}
