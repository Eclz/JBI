<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Department;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentStudentNumberDisplayTest extends TestCase
{
    use RefreshDatabase;

    private function createCourse(): Course
    {
        $department = Department::create([
            'name' => 'Theology Department',
            'code' => 'THEO',
            'is_active' => true,
        ]);

        $academicYear = \App\Models\AcademicYear::create([
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
            'end_date' => '2026-12-20',
            'is_active' => true,
        ]);

        return Course::create([
            'course_code' => 'THEO101',
            'code' => 'THEO101',
            'name' => 'Biblical Foundations',
            'credits' => 3,
            'department_id' => $department->id,
            'semester_id' => $semester->id,
            'status' => 'active',
        ]);
    }

    private function createApplicantRecord(array $attributes = []): Application
    {
        return Application::create(array_merge([
            'application_number' => 'APP' . uniqid(),
            'type' => 'student',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test' . uniqid() . '@example.com',
            'phone' => '1234567890',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'address' => '123 Test Street',
            'status' => 'pending',
        ], $attributes));
    }

    public function test_enrollments_table_displays_student_number_header(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.enrollments.index'));

        $response->assertOk();
        $response->assertSee('Student Number');
        $response->assertDontSee('Admission No.');
    }

    public function test_table_displays_student_number_for_unadmitted_applicants(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->createCourse();

        // Create an unadmitted applicant (has an application with student_number/application_number, but no student_profile admission_number)
        $applicantUser = User::factory()->create([
            'role' => 'student',
            'student_id' => null,
            'name' => 'John Applicant',
            'email' => 'john.applicant@example.com',
        ]);

        $this->createApplicantRecord([
            'application_number' => 'APP20260099',
            'student_number' => 'STU20260099',
            'first_name' => 'John',
            'last_name' => 'Applicant',
            'email' => 'john.applicant@example.com',
            'status' => 'pending', // unadmitted student
            'admission_number' => null,
        ]);

        // Enroll unadmitted student
        CourseEnrollment::create([
            'user_id' => $applicantUser->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.enrollments.index'));

        $response->assertOk();
        $response->assertSee('John Applicant');
        $response->assertSee('STU20260099');
    }

    public function test_table_displays_application_number_if_student_number_not_yet_issued(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->createCourse();

        // Create an applicant who only has application_number
        $applicantUser = User::factory()->create([
            'role' => 'student',
            'student_id' => null,
            'name' => 'Jane Unadmitted',
            'email' => 'jane.unadmitted@example.com',
        ]);

        $this->createApplicantRecord([
            'application_number' => 'STU20260055',
            'student_number' => null,
            'first_name' => 'Jane',
            'last_name' => 'Unadmitted',
            'email' => 'jane.unadmitted@example.com',
            'status' => 'pending',
            'admission_number' => null,
        ]);

        CourseEnrollment::create([
            'user_id' => $applicantUser->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.enrollments.index'));

        $response->assertOk();
        $response->assertSee('Jane Unadmitted');
        $response->assertSee('STU20260055');
    }

    public function test_table_displays_student_id_for_admitted_students(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->createCourse();

        $student = User::factory()->create([
            'role' => 'student',
            'student_id' => 'JBI5544',
            'name' => 'Samuel Admitted',
            'email' => 'samuel.admitted@example.com',
        ]);

        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.enrollments.index'));

        $response->assertOk();
        $response->assertSee('Samuel Admitted');
        $response->assertSee('JBI5544');
    }

    public function test_search_by_student_number_filters_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->createCourse();

        $applicantUser = User::factory()->create([
            'role' => 'student',
            'student_id' => null,
            'name' => 'Alice SearchMe',
            'email' => 'alice@example.com',
        ]);

        $this->createApplicantRecord([
            'application_number' => 'APP20260077',
            'student_number' => 'STU20260077',
            'first_name' => 'Alice',
            'last_name' => 'SearchMe',
            'email' => 'alice@example.com',
            'status' => 'pending',
        ]);

        $otherStudent = User::factory()->create([
            'role' => 'student',
            'student_id' => 'JBI9999',
            'name' => 'Bob Other',
            'email' => 'bob@example.com',
        ]);

        CourseEnrollment::create([
            'user_id' => $applicantUser->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        CourseEnrollment::create([
            'user_id' => $otherStudent->id,
            'course_id' => $course->id,
            'enrollment_date' => '2026-09-01',
            'status' => 'enrolled',
        ]);

        // Search by applicant student number
        $response = $this->actingAs($admin)->get(route('admin.enrollments.index', ['search' => 'STU20260077']));

        $response->assertOk();
        $response->assertSee('Alice SearchMe');
        $response->assertDontSee('Bob Other');
    }

    public function test_create_enrollment_page_has_select2_for_student_and_course(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->createCourse();

        $response = $this->actingAs($admin)->get(route('admin.enrollments.create'));

        $response->assertOk();
        $response->assertSee('id="student_id"', false);
        $response->assertSee('id="course_id"', false);
        $response->assertSee('form-select select2', false);
        $response->assertSee("$('#student_id').select2", false);
        $response->assertSee("$('#course_id').select2", false);
    }
}
