<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Role;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HODResultModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    private function createHODContext(): array
    {
        $hod = User::factory()->create(['role' => 'faculty']);
        $role = Role::where('name', 'hod')->orWhere('slug', 'head_of_department')->first();
        if ($role) {
            $hod->roleCatalog()->associate($role);
            $hod->save();
        }

        $academicYear = AcademicYear::firstOrCreate(
            ['code' => 'AY-2026-2027'],
            [
                'name' => '2026-2027 Academic Year',
                'year' => 2026,
                'start_date' => '2026-09-01',
                'end_date' => '2027-06-30',
                'is_current' => true,
                'is_active' => true,
            ]
        );

        $semester = Semester::firstOrCreate(
            ['name' => 'Fall 2026'],
            [
                'academic_year_id' => $academicYear->id,
                'start_date' => '2026-09-01',
                'end_date' => '2026-12-18',
                'is_active' => true,
            ]
        );

        $faculty = Faculty::factory()->create();
        $department = Department::firstOrCreate(
            ['code' => 'CS-MOD-TEST'],
            [
                'head_of_department_id' => $hod->id,
                'is_active' => true,
                'faculty_id' => $faculty->id,
                'name' => 'Computer Science Department',
                'type' => Department::TYPE_ACADEMIC,
            ]
        );
        $department->update(['head_of_department_id' => $hod->id]);

        $course = Course::create([
            'course_code' => 'CS101',
            'code' => 'CS101',
            'name' => 'Introduction to Programming',
            'department_id' => $department->id,
            'semester_id' => $semester->id,
            'instructor_id' => $hod->id,
            'credits' => 3,
            'status' => 'active',
        ]);

        return compact('hod', 'faculty', 'department', 'course', 'semester', 'academicYear');
    }

    public function test_hod_can_view_operations_hub_with_result_moderation_items(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $course = $ctx['course'];

        $student = User::factory()->create(['role' => 'student']);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Python Control Structures Assignment',
            'description' => 'Practice Python conditional statements and loops.',
            'due_date' => now()->addWeek(),
            'type' => 'homework',
            'max_points' => 50,
            'is_published' => true,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'score' => 45.00,
            'status' => 'graded',
            'submitted_at' => now(),
            'graded_at' => now(),
            'graded_by' => $hod->id,
        ]);

        $response = $this->actingAs($hod)->get(route('faculty.hod.operations.index'));

        $response->assertOk();
        $response->assertSeeText('Head of Department Operations Hub');
        $response->assertSeeText('Python Control Structures Assignment');
        $response->assertSeeText('CS101');
        $response->assertSeeText('Awaiting Moderation');
    }

    public function test_hod_can_moderate_and_approve_assignment_results(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $course = $ctx['course'];
        $student = User::factory()->create(['role' => 'student']);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Database SQL Queries',
            'description' => 'Write complex SQL join statements.',
            'due_date' => now()->addWeek(),
            'type' => 'homework',
            'max_points' => 100,
            'is_published' => true,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'score' => 88.00,
            'status' => 'graded',
            'submitted_at' => now(),
            'graded_at' => now(),
            'graded_by' => $hod->id,
        ]);

        $response = $this->actingAs($hod)->post(route('faculty.hod.operations.results.approve', [
            'type' => 'assignment',
            'id' => $assignment->id,
        ]));

        $response->assertRedirect(route('faculty.hod.operations.index'));
        $response->assertSessionHas('success');

        // Verify Grade record was created, linked, and published
        $this->assertDatabaseHas('grades', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'assignment_id' => $assignment->id,
            'grade_type' => 'assignment',
            'points_earned' => 88.00,
            'points_possible' => 100.00,
            'letter_grade' => 'B',
            'is_published' => true,
        ]);
    }

    public function test_hod_can_moderate_exam_results(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $course = $ctx['course'];
        $student = User::factory()->create(['role' => 'student']);

        $exam = Exam::create([
            'course_id' => $course->id,
            'title' => 'Final Semester Exam',
            'total_marks' => 100,
            'passing_marks' => 50,
            'duration_minutes' => 120,
            'start_time' => now()->subHours(3),
            'end_time' => now()->subHour(),
            'is_published' => true,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'marks_obtained' => 92.00,
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => $hod->id,
        ]);

        $response = $this->actingAs($hod)->post(route('faculty.hod.operations.results.approve', [
            'type' => 'exam',
            'id' => $exam->id,
        ]));

        $response->assertRedirect(route('faculty.hod.operations.index'));

        // Verify Grade was created for exam attempt
        $this->assertDatabaseHas('grades', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'grade_type' => 'exam',
            'points_earned' => 92.00,
            'letter_grade' => 'A',
            'is_published' => true,
        ]);
    }

    public function test_hod_can_moderate_quiz_results(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $course = $ctx['course'];
        $student = User::factory()->create(['role' => 'student']);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'created_by' => $hod->id,
            'title' => 'Midterm Online Quiz',
            'duration_minutes' => 30,
            'passing_marks' => 50,
            'status' => 'published',
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $student->id,
            'score' => 78.00,
            'percentage' => 78.00,
            'status' => 'graded',
        ]);

        $response = $this->actingAs($hod)->post(route('faculty.hod.operations.results.approve', [
            'type' => 'quiz',
            'id' => $quiz->id,
        ]));

        $response->assertRedirect(route('faculty.hod.operations.index'));

        $this->assertDatabaseHas('grades', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'grade_type' => 'quiz',
            'points_earned' => 78.00,
            'letter_grade' => 'C',
            'is_published' => true,
        ]);
    }

    public function test_hod_can_view_detailed_result_moderation_screen_with_student_roster(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $course = $ctx['course'];
        $student = User::factory()->create(['name' => 'Alice Candidate', 'role' => 'student']);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Algorithms & Data Structures Practical',
            'description' => 'An assessment used to validate the HOD result moderation view.',
            'due_date' => now()->addWeek(),
            'type' => 'homework',
            'max_points' => 100,
            'is_published' => true,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'score' => 85.00,
            'status' => 'graded',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($hod)->get(route('faculty.hod.operations.results.show', [
            'type' => 'assignment',
            'id' => $assignment->id,
        ]));

        $response->assertOk();
        $response->assertSeeText('Alice Candidate');
        $response->assertSeeText('Algorithms & Data Structures Practical');
        $response->assertSeeText('85.0');
        $response->assertSeeText('Candidate Marks & Moderation Roster', false);
    }

    public function test_hod_can_adjust_candidate_marks_during_moderation(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $course = $ctx['course'];
        $student = User::factory()->create(['role' => 'student']);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Software Modeling',
            'description' => 'UML Diagrams and Class Specifications.',
            'due_date' => now()->addWeek(),
            'type' => 'homework',
            'max_points' => 100,
            'is_published' => true,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'score' => 65.00,
            'status' => 'graded',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($hod)->post(route('faculty.hod.operations.results.adjust', [
            'type' => 'assignment',
            'id' => $assignment->id,
        ]), [
            'scores' => [
                $submission->id => 72.00,
            ],
        ]);

        $response->assertRedirect();
        $this->assertEquals(72.00, (float) $submission->fresh()->score);
    }

    public function test_hod_can_apply_uniform_curve_scaling(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $course = $ctx['course'];
        $student = User::factory()->create(['role' => 'student']);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Calculus Test',
            'description' => 'Limits and differentiation problems.',
            'due_date' => now()->addWeek(),
            'type' => 'homework',
            'max_points' => 100,
            'is_published' => true,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'score' => 60.00,
            'status' => 'graded',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($hod)->post(route('faculty.hod.operations.results.adjust', [
            'type' => 'assignment',
            'id' => $assignment->id,
        ]), [
            'scale_points' => 5.0,
        ]);

        $response->assertRedirect();
        $this->assertEquals(65.00, (float) $submission->fresh()->adjusted_score);
    }

    public function test_academic_leader_can_access_operations_hub_and_switch_department(): void
    {
        $dean = User::factory()->create(['role' => 'faculty']);
        $ctx = $this->createHODContext();
        $faculty = $ctx['faculty'];
        $department = $ctx['department'];
        $faculty->update(['dean_id' => $dean->id]);

        $response = $this->actingAs($dean)->get(route('faculty.hod.operations.index', [
            'department_id' => $department->id,
        ]));

        $response->assertOk();
        $response->assertSeeText($department->name);
    }
}
