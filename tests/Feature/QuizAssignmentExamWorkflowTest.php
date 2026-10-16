<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\Role;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuizAssignmentExamWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Storage::fake('public');
    }

    private function createAcademicContext(): array
    {
        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'year' => 2026,
            'start_date' => Carbon::now()->subMonths(2),
            'end_date' => Carbon::now()->addMonths(10),
            'is_current' => true,
        ]);

        $semester = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Semester 1',
            'start_date' => Carbon::now()->subMonths(1),
            'end_date' => Carbon::now()->addMonths(5),
            'is_current' => true,
        ]);

        $department = Department::factory()->create();
        $program = Program::create([
            'name' => 'Software Engineering',
            'code' => 'SE101',
            'department_id' => $department->id,
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Faculty Full',
            'slug' => 'faculty_full',
            'guard_role' => 'faculty',
            'permissions' => [
                'exams' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                'assignments' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                'quizzes' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                'grades' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
            ],
            'is_active' => true,
        ]);

        $lecturer = User::factory()->create([
            'role' => 'faculty',
            'role_id' => $role->id,
            'name' => 'Dr. Lecturer',
            'email' => 'lecturer@example.com',
        ]);

        $studentUser = User::factory()->create([
            'role' => 'student',
            'name' => 'John Student',
            'email' => 'student@example.com',
        ]);

        $studentProfile = StudentProfile::factory()->active()->create([
            'user_id' => $studentUser->id,
        ]);

        $feeStructure = FeeStructure::factory()->create(['type' => 'tuition']);

        FeeRecord::create([
            'user_id' => $studentUser->id,
            'fee_structure_id' => $feeStructure->id,
            'invoice_number' => 'INV-TEST-001',
            'amount' => 1000,
            'total_amount' => 1000,
            'paid_amount' => 1000,
            'balance_amount' => 0,
            'type' => 'tuition',
            'status' => 'paid',
            'due_date' => now()->addDays(30),
        ]);

        $course = Course::create([
            'course_code' => 'CS301',
            'name' => 'Algorithms',
            'department_id' => $department->id,
            'semester_id' => $semester->id,
            'instructor_id' => $lecturer->id,
            'credits' => 3,
        ]);

        CourseEnrollment::create([
            'user_id' => $studentUser->id,
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'status' => 'enrolled',
            'enrollment_date' => now(),
        ]);

        return compact('lecturer', 'studentUser', 'studentProfile', 'course', 'semester', 'academicYear');
    }

    public function test_lecturer_can_close_reopen_and_change_status_of_quiz(): void
    {
        $ctx = $this->createAcademicContext();

        $quiz = Quiz::create([
            'course_id' => $ctx['course']->id,
            'created_by' => $ctx['lecturer']->id,
            'title' => 'Midterm Quiz',
            'duration_minutes' => 30,
            'total_marks' => 100,
            'passing_percentage' => 50,
            'is_published' => true,
            'status' => 'published',
            'start_time' => Carbon::now()->subHour(),
            'end_time' => Carbon::now()->addHours(2),
        ]);

        $this->assertTrue($quiz->isActive());

        // Close quiz
        $this->actingAs($ctx['lecturer'])
            ->post(route('faculty.quizzes.close', $quiz))
            ->assertRedirect();

        $quiz->refresh();
        $this->assertEquals('closed', $quiz->status);
        $this->assertFalse($quiz->isActive());

        // Reopen quiz
        $this->actingAs($ctx['lecturer'])
            ->post(route('faculty.quizzes.reopen', $quiz))
            ->assertRedirect();

        $quiz->refresh();
        $this->assertEquals('active', $quiz->status);
        $this->assertTrue($quiz->isActive());

        // Update status to active
        $this->actingAs($ctx['lecturer'])
            ->post(route('faculty.quizzes.status.update', $quiz), [
                'status' => 'active',
            ])
            ->assertRedirect();

        $quiz->refresh();
        $this->assertEquals('active', $quiz->status);
        $this->assertTrue($quiz->isActive());
    }

    public function test_student_can_attempt_active_quiz_and_is_blocked_when_closed(): void
    {
        $ctx = $this->createAcademicContext();

        $quiz = Quiz::create([
            'course_id' => $ctx['course']->id,
            'created_by' => $ctx['lecturer']->id,
            'title' => 'Data Structures Quiz',
            'duration_minutes' => 45,
            'total_marks' => 20,
            'passing_percentage' => 50,
            'is_published' => true,
            'status' => 'published',
            'start_time' => Carbon::now()->subHour(),
            'end_time' => Carbon::now()->addHours(2),
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'What is O(1)?',
            'question_type' => 'multiple_choice',
            'points' => 10,
            'options' => ['Constant time', 'Linear time'],
            'correct_answer' => 'Constant time',
            'order' => 1,
        ]);

        // Student starts quiz
        $response = $this->actingAs($ctx['studentUser'])
            ->post(route('student.quizzes.start', $quiz));

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $ctx['studentUser']->id)->first();
        $this->assertNotNull($attempt);
        $response->assertRedirect(route('student.quizzes.take', [$quiz, $attempt]));

        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $quiz->id,
            'user_id' => $ctx['studentUser']->id,
            'status' => 'in_progress',
        ]);

        // Lecturer closes quiz
        $quiz->update(['status' => 'closed']);

        // Another student cannot attempt closed quiz
        $anotherStudent = User::factory()->create(['role' => 'student']);
        CourseEnrollment::create([
            'user_id' => $anotherStudent->id,
            'course_id' => $ctx['course']->id,
            'semester_id' => $ctx['semester']->id,
            'status' => 'enrolled',
            'enrollment_date' => now(),
        ]);

        $this->actingAs($anotherStudent)
            ->post(route('student.quizzes.start', $quiz))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_assignment_submission_becomes_ungraded_and_transitions_to_graded_upon_lecturer_grading(): void
    {
        $ctx = $this->createAcademicContext();

        $assignment = Assignment::create([
            'course_id' => $ctx['course']->id,
            'title' => 'Project Assignment 1',
            'description' => 'Write an algorithm.',
            'due_date' => Carbon::now()->addDays(5),
            'max_score' => 100,
            'points' => 100,
            'weight_percentage' => 20,
            'submission_type' => 'text',
        ]);

        // Student submits assignment
        $this->actingAs($ctx['studentUser'])
            ->post(route('student.assignments.submit', $assignment), [
                'submission_text' => 'Here is my complete algorithm implementation.',
                'action' => 'submit',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', $ctx['studentUser']->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals('ungraded', $submission->status);
        $this->assertEquals('Ungraded', $submission->status_label);
        $this->assertEquals('ungraded', $submission->effective_status);

        // Lecturer grades the submission
        $this->actingAs($ctx['lecturer'])
            ->post(route('faculty.assignments.submissions.grade', [$assignment, $submission]), [
                'score' => 88,
                'feedback' => 'Well structured code.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $submission->refresh();
        $this->assertEquals('graded', $submission->status);
        $this->assertEquals('Graded', $submission->status_label);
        $this->assertEquals('graded', $submission->effective_status);
        $this->assertEquals(88, $submission->score);
    }

    public function test_exam_changes_status_to_closed_when_due_date_passes(): void
    {
        $ctx = $this->createAcademicContext();

        $activeExam = Exam::create([
            'course_id' => $ctx['course']->id,
            'title' => 'Current Active Exam',
            'duration_minutes' => 60,
            'total_marks' => 100,
            'passing_marks' => 50,
            'start_time' => Carbon::now()->subMinutes(30),
            'end_time' => Carbon::now()->addMinutes(30),
            'is_published' => true,
            'exam_mode' => 'online',
        ]);

        $this->assertEquals('active', $activeExam->effective_status);
        $this->assertEquals('Active / In Progress', $activeExam->status_label);
        $this->assertTrue($activeExam->isActive());

        $pastExam = Exam::create([
            'course_id' => $ctx['course']->id,
            'title' => 'Past Concluded Exam',
            'duration_minutes' => 60,
            'total_marks' => 100,
            'passing_marks' => 50,
            'start_time' => Carbon::now()->subHours(5),
            'end_time' => Carbon::now()->subHours(2),
            'is_published' => true,
            'exam_mode' => 'online',
        ]);

        $this->assertEquals('closed', $pastExam->effective_status);
        $this->assertEquals('Closed / Ended', $pastExam->status_label);
        $this->assertFalse($pastExam->isActive());
    }

    public function test_student_cannot_download_question_paper_for_online_exams(): void
    {
        $ctx = $this->createAcademicContext();

        Storage::disk('public')->put('exams/online_paper.pdf', 'Sample exam questions');

        $onlineExam = Exam::create([
            'course_id' => $ctx['course']->id,
            'title' => 'Online Final Examination',
            'duration_minutes' => 120,
            'total_marks' => 100,
            'passing_marks' => 50,
            'start_time' => Carbon::now()->subHour(),
            'end_time' => Carbon::now()->addHours(2),
            'is_published' => true,
            'exam_mode' => 'online',
            'exam_paper_url' => 'exams/online_paper.pdf',
        ]);

        $this->actingAs($ctx['studentUser'])
            ->get(route('student.exams.download-paper', $onlineExam))
            ->assertRedirect()
            ->assertSessionHas('error', 'Question papers for online examinations cannot be downloaded. Please attempt the exam online in the system.');
    }

    public function test_student_cannot_download_question_paper_nor_answers_for_physical_exams(): void
    {
        $ctx = $this->createAcademicContext();

        Storage::disk('public')->put('exams/physical_paper.pdf', 'Physical question paper');
        Storage::disk('public')->put('exams/physical_booklet.pdf', 'Physical booklet');

        $physicalExam = Exam::create([
            'course_id' => $ctx['course']->id,
            'title' => 'Physical In-Person Exam',
            'duration_minutes' => 120,
            'total_marks' => 100,
            'passing_marks' => 50,
            'start_time' => Carbon::now()->subHour(),
            'end_time' => Carbon::now()->addHours(2),
            'is_published' => true,
            'exam_mode' => 'offline',
            'room_number' => 'Hall 4',
            'exam_paper_url' => 'exams/physical_paper.pdf',
            'answer_booklet_url' => 'exams/physical_booklet.pdf',
        ]);

        // Question paper download blocked
        $this->actingAs($ctx['studentUser'])
            ->get(route('student.exams.download-paper', $physicalExam))
            ->assertRedirect()
            ->assertSessionHas('error', 'Question papers for physical/offline examinations cannot be downloaded.');

        // Answer booklet download blocked
        $this->actingAs($ctx['studentUser'])
            ->get(route('student.exams.download-booklet', $physicalExam))
            ->assertRedirect()
            ->assertSessionHas('error', 'Answer booklets for physical/offline examinations cannot be downloaded.');
    }

    public function test_student_cannot_download_answer_booklet_before_attempted_or_closed(): void
    {
        $ctx = $this->createAcademicContext();

        Storage::disk('public')->put('exams/hybrid_booklet.pdf', 'Marking Guide');

        $hybridExam = Exam::create([
            'course_id' => $ctx['course']->id,
            'title' => 'Hybrid Architecture Exam',
            'duration_minutes' => 120,
            'total_marks' => 100,
            'passing_marks' => 50,
            'start_time' => Carbon::now()->subHour(),
            'end_time' => Carbon::now()->addHours(2),
            'is_published' => true,
            'exam_mode' => 'hybrid',
            'answer_booklet_url' => 'exams/hybrid_booklet.pdf',
        ]);

        // Before attempt or closed: blocked
        $this->actingAs($ctx['studentUser'])
            ->get(route('student.exams.download-booklet', $hybridExam))
            ->assertRedirect()
            ->assertSessionHas('error', 'Answer booklets cannot be downloaded before the exam has been attempted or closed.');

        // Now attempt is submitted: allowed
        ExamAttempt::create([
            'exam_id' => $hybridExam->id,
            'user_id' => $ctx['studentUser']->id,
            'status' => 'submitted',
            'started_at' => Carbon::now()->subHour(),
            'submitted_at' => Carbon::now()->subMinutes(10),
        ]);

        $response = $this->actingAs($ctx['studentUser'])
            ->get(route('student.exams.download-booklet', $hybridExam));

        $this->assertNotEquals(302, $response->getStatusCode());
    }

    public function test_student_cannot_start_or_take_physical_exam_online(): void
    {
        $ctx = $this->createAcademicContext();

        $physicalExam = Exam::create([
            'course_id' => $ctx['course']->id,
            'title' => 'Physical Chemistry Exam',
            'duration_minutes' => 90,
            'total_marks' => 100,
            'passing_marks' => 50,
            'start_time' => Carbon::now()->subMinutes(30),
            'end_time' => Carbon::now()->addHours(1),
            'is_published' => true,
            'exam_mode' => 'offline',
            'room_number' => 'Lab B',
        ]);

        // Attempting to start online is blocked
        $this->actingAs($ctx['studentUser'])
            ->post(route('student.exams.start', $physicalExam))
            ->assertRedirect()
            ->assertSessionHas('error');

        // Attempting to access take page is blocked
        $this->actingAs($ctx['studentUser'])
            ->get(route('student.exams.take', $physicalExam))
            ->assertRedirect(route('student.exams.show', $physicalExam))
            ->assertSessionHas('error');
    }

    public function test_student_cannot_submit_exam_beyond_time_limit(): void
    {
        $ctx = $this->createAcademicContext();

        $exam = Exam::create([
            'course_id' => $ctx['course']->id,
            'title' => 'Speed Testing Exam',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'passing_marks' => 25,
            'start_time' => Carbon::now()->subHours(2),
            'end_time' => Carbon::now()->addHours(1),
            'is_published' => true,
            'exam_mode' => 'online',
        ]);

        // Attempt started 45 minutes ago (duration was 30 mins, exceeded by 15 mins)
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $ctx['studentUser']->id,
            'status' => 'in_progress',
            'started_at' => Carbon::now()->subMinutes(45),
        ]);

        $this->actingAs($ctx['studentUser'])
            ->post(route('student.exams.submit', $exam), [
                'answers' => 'My late answers',
            ])
            ->assertRedirect(route('student.exams.show', $exam))
            ->assertSessionHas('error', 'The examination time has expired. Submissions beyond the exam time limit are not accepted.');

        $attempt->refresh();
        $this->assertEquals('submitted', $attempt->status);
        $this->assertNull($attempt->answers);
    }
}
