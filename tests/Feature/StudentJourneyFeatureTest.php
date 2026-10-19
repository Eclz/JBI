<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentJourneyFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_eligible_student_can_register_for_a_course_in_the_open_current_semester(): void
    {
        $student = User::factory()->student()->create();
        $profile = StudentProfile::factory()->active()->create([
            'user_id' => $student->id,
            'registration_fee_paid_at' => now(),
            'year_of_study' => 1,
        ]);
        $semester = Semester::factory()->current()->create([
            'is_active' => true,
            'registration_start' => now()->subDay()->toDateString(),
            'registration_end' => now()->addDays(7)->toDateString(),
        ]);
        $course = Course::factory()->active()->create([
            'department_id' => $profile->department_id,
            'semester_id' => $semester->id,
            'year_of_study' => 1,
        ]);

        $this->actingAs($student)
            ->from(route('student.courses.index'))
            ->post(route('student.courses.enroll', $course))
            ->assertRedirect(route('student.courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);
    }

    public function test_course_registration_rejects_unpaid_registration_fee_and_wrong_study_year(): void
    {
        $student = User::factory()->student()->create();
        $profile = StudentProfile::factory()->active()->create([
            'user_id' => $student->id,
            'registration_fee_paid_at' => null,
            'year_of_study' => 1,
        ]);
        $semester = Semester::factory()->current()->create([
            'is_active' => true,
            'registration_start' => now()->subDay()->toDateString(),
            'registration_end' => now()->addDays(7)->toDateString(),
        ]);
        $course = Course::factory()->active()->create([
            'department_id' => $profile->department_id,
            'semester_id' => $semester->id,
            'year_of_study' => 1,
        ]);

        $this->actingAs($student)
            ->from(route('student.courses.index'))
            ->post(route('student.courses.enroll', $course))
            ->assertRedirect(route('student.courses.index'))
            ->assertSessionHasErrors();

        $profile->update(['registration_fee_paid_at' => now()]);
        $course->update(['year_of_study' => 2]);

        $this->from(route('student.courses.index'))
            ->post(route('student.courses.enroll', $course))
            ->assertRedirect(route('student.courses.index'))
            ->assertSessionHasErrors();

        $this->assertDatabaseMissing('course_enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_student_transcript_contains_published_grades_only(): void
    {
        $student = User::factory()->student()->create();
        StudentProfile::factory()->active()->create(['user_id' => $student->id]);
        $anotherStudent = User::factory()->student()->create();
        StudentProfile::factory()->active()->create(['user_id' => $anotherStudent->id]);
        $course = Course::factory()->create();
        $publishedGrade = Grade::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'assignment_id' => null,
            'grade_type' => 'quiz',
            'graded_by' => $course->instructor_id,
            'is_published' => true,
        ]);
        $unpublishedGrade = Grade::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'assignment_id' => null,
            'grade_type' => 'quiz',
            'graded_by' => $course->instructor_id,
            'is_published' => false,
        ]);
        $anotherStudentsGrade = Grade::factory()->create([
            'user_id' => $anotherStudent->id,
            'course_id' => $course->id,
            'assignment_id' => null,
            'grade_type' => 'quiz',
            'graded_by' => $course->instructor_id,
            'is_published' => true,
        ]);

        $this->actingAs($student)
            ->get(route('student.transcript'))
            ->assertOk()
            ->assertViewHas('grades', function ($grades) use ($publishedGrade, $unpublishedGrade, $anotherStudentsGrade): bool {
                $transcriptGrades = $grades->flatten();

                return $transcriptGrades->contains('id', $publishedGrade->id)
                    && !$transcriptGrades->contains('id', $unpublishedGrade->id)
                    && !$transcriptGrades->contains('id', $anotherStudentsGrade->id);
            });
    }

    public function test_enrolled_student_can_access_published_course_content_and_submit_assignment(): void
    {
        $student = User::factory()->student()->create();
        StudentProfile::factory()->active()->create(['user_id' => $student->id]);
        $course = Course::factory()->create();
        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'enrolled',
        ]);
        $publishedMaterial = CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'uploaded_by' => $course->instructor_id,
            'is_published' => true,
        ]);
        $unpublishedMaterial = CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'uploaded_by' => $course->instructor_id,
            'is_published' => false,
        ]);
        $publishedGrade = Grade::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'assignment_id' => null,
            'grade_type' => 'quiz',
            'graded_by' => $course->instructor_id,
            'is_published' => true,
        ]);
        $assignment = Assignment::factory()->published()->create([
            'course_id' => $course->id,
            'available_from' => now()->subDay(),
            'due_date' => now()->addDay(),
            'available_until' => null,
        ]);

        $this->actingAs($student)
            ->get(route('student.courses.materials', $course))
            ->assertOk()
            ->assertViewHas('materials', function ($materials) use ($publishedMaterial, $unpublishedMaterial): bool {
                return $materials->contains('id', $publishedMaterial->id)
                    && !$materials->contains('id', $unpublishedMaterial->id);
            });

        $this->get(route('student.courses.grades', $course))
            ->assertOk()
            ->assertViewHas('grades', function ($grades) use ($publishedGrade): bool {
                return $grades->contains('id', $publishedGrade->id);
            });

        $this->post(route('student.assignments.submit', $assignment), [
            'submission_text' => 'My completed assignment.',
        ])->assertRedirect();

        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'submission_text' => 'My completed assignment.',
            'status' => 'ungraded',
        ]);

        $anotherStudent = User::factory()->student()->create();
        StudentProfile::factory()->active()->create(['user_id' => $anotherStudent->id]);

        $this->actingAs($anotherStudent)
            ->get(route('student.assignments.show', $assignment))
            ->assertNotFound();
    }
}
