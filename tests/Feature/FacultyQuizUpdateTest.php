<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacultyQuizUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_faculty_can_save_quiz_edits_and_existing_passing_marks_are_prepopulated(): void
    {
        $role = Role::create([
            'name' => 'Quiz Editor',
            'slug' => 'quiz_editor',
            'guard_role' => 'faculty',
            'permissions' => ['exams' => ['view' => true, 'edit' => true]],
            'is_active' => true,
        ]);

        $faculty = User::factory()->create([
            'role' => 'faculty',
            'role_id' => $role->id,
        ]);
        $course = Course::factory()->withInstructor($faculty)->create();
        $quiz = Quiz::create([
            'course_id' => $course->id,
            'created_by' => $faculty->id,
            'title' => 'Original quiz',
            'duration_minutes' => 30,
            'total_marks' => 100,
            'passing_marks' => 60,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
            'max_attempts' => 1,
        ]);

        $this->actingAs($faculty)
            ->get(route('faculty.quizzes.edit', $quiz))
            ->assertOk()
            ->assertSee('value="60"', false);

        $this->actingAs($faculty)
            ->put(route('faculty.quizzes.update', $quiz), [
                'title' => 'Updated quiz',
                'description' => 'Updated description',
                'duration_minutes' => 45,
                'total_marks' => 120,
                'passing_marks' => 75,
                'start_time' => now()->addDays(3)->format('Y-m-d H:i:s'),
                'end_time' => now()->addDays(4)->format('Y-m-d H:i:s'),
                'max_attempts' => 2,
                'show_results' => 'after_deadline',
                'shuffle_questions' => '1',
                'shuffle_options' => '1',
            ])
            ->assertRedirect(route('faculty.quizzes.show', $quiz));

        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'title' => 'Updated quiz',
            'passing_marks' => 75,
            'show_results' => 'after_deadline',
            'shuffle_questions' => true,
            'shuffle_options' => true,
        ]);
    }
}
