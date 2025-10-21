<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseMaterial;
use App\Models\Course;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseMaterial>
 */
class CourseMaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $materialTypes = [
            'document' => [
                'Lecture Notes - Chapter 1',
                'Reading Assignment Guidelines',
                'Study Guide for Midterm',
                'Course Syllabus',
                'Supplementary Reading List',
            ],
            'video' => [
                'Lecture Recording - Introduction',
                'Guest Speaker Presentation',
                'Biblical Archaeology Documentary',
                'Theological Discussion Panel',
                'Ministry Training Video',
            ],
            'presentation' => [
                'PowerPoint - Biblical Hermeneutics',
                'Slides - Church History Timeline',
                'Presentation - Systematic Theology',
                'Visual Aid - Biblical Geography',
                'Infographic - Theological Concepts',
            ],
            'link' => [
                'Online Bible Commentary',
                'Academic Journal Article',
                'Video Lecture Series',
                'Research Database Access',
                'Digital Library Resource',
            ],
        ];

        $type = fake()->randomElement(array_keys($materialTypes));
        $title = fake()->randomElement($materialTypes[$type]);
        
        $course = Course::inRandomOrder()->first() ?? Course::factory()->create();
        $uploader = $course->instructor ?? User::factory()->faculty()->create();

        $fileExtensions = [
            'document' => ['pdf', 'doc', 'docx', 'txt'],
            'video' => ['mp4', 'avi', 'mov', 'wmv'],
            'presentation' => ['ppt', 'pptx', 'pdf'],
            'link' => [null], // No file for links
        ];

        $extension = fake()->randomElement($fileExtensions[$type]);
        $fileName = $extension ? fake()->words(3, true) . '.' . $extension : null;
        $filePath = $fileName ? 'course_materials/' . fake()->uuid() . '/' . $fileName : null;

        return [
            'course_id' => $course->id,
            'title' => $title,
            'description' => fake()->optional(0.8)->paragraph(),
            'type' => $type,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $extension,
            'file_size' => $fileName ? fake()->numberBetween(100000, 50000000) : null, // bytes
            'external_url' => $type === 'link' ? fake()->url() : null,
            'is_downloadable' => $type !== 'link',
            'is_published' => fake()->boolean(90),
            'available_from' => fake()->optional(0.3)->dateTimeBetween('-30 days', 'now'),
            'available_until' => fake()->optional(0.2)->dateTimeBetween('now', '+90 days'),
            'download_count' => fake()->numberBetween(0, 150),
            'view_count' => fake()->numberBetween(0, 300),
            'access_permissions' => fake()->optional(0.3)->randomElements(['student', 'faculty'], fake()->numberBetween(1, 2)),
            'uploaded_by' => $uploader->id,
        ];
    }

    /**
     * Create published material
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Create material of specific type
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }
}
