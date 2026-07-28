<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $courses = [
            // Biblical Studies
            ['prefix' => 'BIBL', 'courses' => [
                'Introduction to Biblical Studies',
                'Old Testament Survey',
                'New Testament Survey',
                'Biblical Hermeneutics',
                'Exegetical Methods',
                'Biblical Archaeology',
            ]],
            // Theology
            ['prefix' => 'THEO', 'courses' => [
                'Systematic Theology I',
                'Systematic Theology II',
                'Christian Doctrine',
                'Apologetics',
                'Contemporary Theology',
                'Reformed Theology',
            ]],
            // Church History
            ['prefix' => 'HIST', 'courses' => [
                'Early Church History',
                'Medieval Church History',
                'Reformation History',
                'Modern Church History',
                'American Christianity',
                'World Christianity',
            ]],
            // Christian Ministry
            ['prefix' => 'MINI', 'courses' => [
                'Introduction to Ministry',
                'Pastoral Leadership',
                'Church Administration',
                'Evangelism and Discipleship',
                'Youth Ministry',
                'Worship Leadership',
            ]],
            // Biblical Languages
            ['prefix' => 'LANG', 'courses' => [
                'Biblical Hebrew I',
                'Biblical Hebrew II',
                'New Testament Greek I',
                'New Testament Greek II',
                'Advanced Hebrew Exegesis',
                'Advanced Greek Exegesis',
            ]],
        ];

        $courseGroup = fake()->randomElement($courses);
        $courseName = fake()->randomElement($courseGroup['courses']);
        do {
            $courseNumber = fake()->numberBetween(101, 999);
            $code = $courseGroup['prefix'] . $courseNumber;
        } while (Course::where('course_code', $code)->orWhere('code', $code)->exists());

        $department = Department::where('code', $courseGroup['prefix'])->first() 
                     ?? Department::factory()->create(['code' => $courseGroup['prefix']]);

        $semester = Semester::inRandomOrder()->first() ?? Semester::factory()->create();
        $instructor = User::where('role', 'faculty')->inRandomOrder()->first() 
                     ?? User::factory()->faculty()->create();

        // Generate realistic schedule
        $days = fake()->randomElements(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], fake()->numberBetween(2, 3));
        $schedule = [];
        $startTime = fake()->randomElement(['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00']);
        $endTime = date('H:i', strtotime($startTime . ' +1 hour 30 minutes'));

        foreach ($days as $day) {
            $schedule[$day] = [
                'start' => $startTime,
                'end' => $endTime,
            ];
        }

        return [
            'code' => $code,
            'course_code' => $code,
            'name' => $courseName,
            'description' => fake()->paragraph(3),
            'credits' => fake()->randomElement([1, 2, 3, 4]),
            'department_id' => $department->id,
            'instructor_id' => $instructor->id,
            'semester_id' => $semester->id,
            'schedule' => $schedule,
            'room' => fake()->randomElement([
                'Johnson Hall 101', 'Johnson Hall 102', 'Johnson Hall 201', 'Johnson Hall 202',
                'Academic Building A-101', 'Academic Building A-102', 'Academic Building B-101',
                'Seminary Room 1', 'Seminary Room 2', 'Chapel', 'Library Conference Room',
            ]),
            'max_students' => fake()->numberBetween(15, 35),
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive']), // 75% active
            'fee_amount' => fake()->randomFloat(2, 500, 2000),
            'learning_objectives' => fake()->paragraphs(3, true),
            'assessment_methods' => fake()->paragraph(2),
            'prerequisites' => fake()->optional(0.3)->randomElements([1, 2, 3], fake()->numberBetween(0, 2)),
        ];
    }

    /**
     * Create active course
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function forDepartment(Department $department): static
    {
        return $this->state(function (array $attributes) use ($department) {
            do {
                $code = $department->code . fake()->numberBetween(101, 999);
            } while (Course::where('course_code', $code)->orWhere('code', $code)->exists());
            
            return [
                'department_id' => $department->id,
                'code' => $code,
                'course_code' => $code,
            ];
        });
    }

    /**
     * Create course with specific instructor
     */
    public function withInstructor(User $instructor): static
    {
        return $this->state(fn (array $attributes) => [
            'instructor_id' => $instructor->id,
        ]);
    }
}
