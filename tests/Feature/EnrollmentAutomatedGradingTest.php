<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentAutomatedGradingTest extends TestCase
{
    use RefreshDatabase;

    private function createCourseWithSemester(): array
    {
        $department = Department::create([
            'name' => 'Theology Department',
            'code' => 'THEO',
            'is_active' => true,
        ]);

        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'year' => 2026,
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
            'is_active' => true,
        ]);

        $semester = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Fall 2026',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-18',
            'is_active' => true,
        ]);

        $course = Course::create([
            'course_code' => 'THEO201',
            'code' => 'THEO201',
            'name' => 'Systematic Theology',
            'credits' => 3,
            'department_id' => $department->id,
            'semester_id' => $semester->id,
            'status' => 'active',
        ]);

        return [$course, $semester];
    }

    public function test_course_enrollment_student_id_query_compatibility(): void
    {
        [$course] = $this->createCourseWithSemester();
        $student = User::factory()->create(['role' => 'student']);

        $enrollment = CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        // Verify that where('student_id', ...) does not throw SQLSTATE[42S22] unknown column error
        $found = CourseEnrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        $this->assertNotNull($found);
        $this->assertEquals($enrollment->id, $found->id);

        $exists = CourseEnrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->exists();

        $this->assertTrue($exists);
    }

    public function test_status_badge_classes_use_bootstrap5_background_utilities(): void
    {
        [$course] = $this->createCourseWithSemester();
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $student3 = User::factory()->create(['role' => 'student']);

        $enrolled = CourseEnrollment::create([
            'user_id' => $student1->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        $completed = CourseEnrollment::create([
            'user_id' => $student2->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'completed',
        ]);

        $dropped = CourseEnrollment::create([
            'user_id' => $student3->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'dropped',
        ]);

        $this->assertStringContainsString('bg-success text-white', $enrolled->getStatusBadgeClass());
        $this->assertStringContainsString('bg-primary text-white', $completed->getStatusBadgeClass());
        $this->assertStringContainsString('bg-danger text-white', $dropped->getStatusBadgeClass());

        // Verify on show page http://127.0.0.1:8000/admin/enrollments/{id}
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('admin.enrollments.show', $enrolled));

        $response->assertOk();
        $response->assertSee('bg-success text-white');
    }

    public function test_enrollment_edit_page_renders_automated_grading_info_and_criteria(): void
    {
        [$course, $semester] = $this->createCourseWithSemester();
        $student = User::factory()->create(['role' => 'student']);

        $enrollment = CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('admin.enrollments.edit', $enrollment));

        $response->assertOk();
        $response->assertSee('Academic Registrar Automated Grading');
        $response->assertSee('Registrar Grading Criteria');
        $response->assertSee('Dec 18, 2026');
        // Verify manual input fields for final_grade, letter_grade, grade_points have been removed
        $response->assertDontSee('name="final_grade"', false);
        $response->assertDontSee('name="letter_grade"', false);
        $response->assertDontSee('name="grade_points"', false);
    }

    public function test_enrollment_update_automatically_calculates_grades_and_sets_completion_date(): void
    {
        [$course, $semester] = $this->createCourseWithSemester();
        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'admin']);

        $enrollment = CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        // Add student grades for this course (85% average)
        Grade::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'grade_type' => 'exam',
            'points_earned' => 85.00,
            'points_possible' => 100.00,
            'percentage' => 85.00,
            'letter_grade' => 'A',
            'grade_points' => 4.00,
            'graded_at' => now(),
            'graded_by' => $admin->id,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        // Update enrollment to completed
        $response = $this->actingAs($admin)->put(route('admin.enrollments.update', $enrollment), [
            'status' => 'completed',
            'enrollment_date' => '2026-09-01',
            'notes' => 'Completed coursework successfully.',
        ]);

        $response->assertRedirect(route('admin.enrollments.index'));

        $enrollment->refresh();

        // Grade should be calculated to 85.00%, letter grade to 'A' (85-89.99 default is A), grade points to 4.00
        $this->assertEquals(85.00, (float) $enrollment->final_grade);
        $this->assertEquals('A', $enrollment->letter_grade);
        $this->assertEquals(4.00, (float) $enrollment->grade_points);

        // Completion date should be automatically set to the semester closure date: 2026-12-18
        $this->assertNotNull($enrollment->completion_date);
        $this->assertEquals('2026-12-18', $enrollment->completion_date->format('Y-m-d'));
    }

    public function test_academic_registrar_can_update_grading_criteria(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Test GET grading criteria route
        $response = $this->actingAs($admin)->get(route('admin.registrar.grading-criteria'));
        $response->assertOk();
        $response->assertSee('Academic Registrar Grading Criteria');

        // Update grading criteria via POST
        $newCriteria = [
            ['min' => 90, 'max' => 100, 'letter' => 'A+', 'grade_points' => 4.0, 'remark' => 'Distinction'],
            ['min' => 80, 'max' => 89.99, 'letter' => 'A', 'grade_points' => 3.75, 'remark' => 'Excellent'],
            ['min' => 70, 'max' => 79.99, 'letter' => 'B', 'grade_points' => 3.0, 'remark' => 'Good'],
            ['min' => 0, 'max' => 69.99, 'letter' => 'F', 'grade_points' => 0.0, 'remark' => 'Fail'],
        ];

        $postResponse = $this->actingAs($admin)->post(route('admin.registrar.grading-criteria.update'), [
            'criteria' => $newCriteria,
        ]);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');

        // Check that the criteria were saved in settings
        $savedCriteria = CourseEnrollment::getAcademicRegistrarGradingCriteria();
        $this->assertCount(4, $savedCriteria);
        $this->assertEquals('A+', $savedCriteria[0]['letter']);
        $this->assertEquals(4.0, (float) $savedCriteria[0]['grade_points']);

        // Verify letter grade and grade points evaluation against the newly configured criteria
        $letterGrade = CourseEnrollment::determineLetterGrade(85.0, $savedCriteria);
        $gradePoint = CourseEnrollment::determineGradePoints(85.0, $savedCriteria);

        $this->assertEquals('A', $letterGrade);
        $this->assertEquals(3.75, $gradePoint);
    }

    public function test_enrollment_type_indicators_support_normal_retake_missed_paper_and_redo(): void
    {
        [$course] = $this->createCourseWithSemester();
        $student = User::factory()->create(['role' => 'student']);

        $normal = new CourseEnrollment(['enrollment_type' => 'normal']);
        $retake = new CourseEnrollment(['enrollment_type' => 'retake']);
        $missedPaper = new CourseEnrollment(['enrollment_type' => 'missed_paper']);
        $redo = new CourseEnrollment(['enrollment_type' => 'redo']);

        $this->assertEquals('Normal', $normal->getEnrollmentTypeText());
        $this->assertEquals('Retake', $retake->getEnrollmentTypeText());
        $this->assertEquals('Missed Paper', $missedPaper->getEnrollmentTypeText());
        $this->assertEquals('Redo', $redo->getEnrollmentTypeText());

        $this->assertStringContainsString('bg-secondary', $normal->getEnrollmentTypeBadgeClass());
        $this->assertStringContainsString('bg-warning', $retake->getEnrollmentTypeBadgeClass());
        $this->assertStringContainsString('bg-danger', $missedPaper->getEnrollmentTypeBadgeClass());
        $this->assertStringContainsString('bg-info', $redo->getEnrollmentTypeBadgeClass());
    }

    public function test_enrollment_creation_and_update_with_retake_missed_paper_and_redo(): void
    {
        [$course] = $this->createCourseWithSemester();
        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create a retake enrollment
        $response = $this->actingAs($admin)->post(route('admin.enrollments.store'), [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
            'enrollment_type' => 'retake',
        ]);

        $response->assertRedirect(route('admin.enrollments.index'));
        $this->assertDatabaseHas('course_enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_type' => 'retake',
        ]);

        $enrollment = CourseEnrollment::where('user_id', $student->id)->where('course_id', $course->id)->first();

        // 2. View show page and ensure "Retake" badge is present
        $showResponse = $this->actingAs($admin)->get(route('admin.enrollments.show', $enrollment));
        $showResponse->assertOk();
        $showResponse->assertSee('Unit Nature / Type');
        $showResponse->assertSee('Retake');

        // 3. View index page and ensure "Unit Nature" column and "Retake" badge are present
        $indexResponse = $this->actingAs($admin)->get(route('admin.enrollments.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Unit Nature');
        $indexResponse->assertSee('Retake');

        // 4. Update enrollment to "redo"
        $updateResponse = $this->actingAs($admin)->put(route('admin.enrollments.update', $enrollment), [
            'status' => 'enrolled',
            'enrollment_type' => 'redo',
            'enrollment_date' => '2026-09-01',
        ]);

        $updateResponse->assertRedirect(route('admin.enrollments.index'));
        $enrollment->refresh();
        $this->assertEquals('redo', $enrollment->enrollment_type);
        $this->assertEquals('Redo', $enrollment->getEnrollmentTypeText());
    }
}
