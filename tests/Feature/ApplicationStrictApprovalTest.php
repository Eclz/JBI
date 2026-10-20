<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Application;
use App\Models\Program;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationStrictApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Program $program;
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'role_id' => null,
        ]);

        $this->department = Department::factory()->create(['code' => 'CS', 'name' => 'Computer Science']);
        $this->program = Program::create([
            'department_id' => $this->department->id,
            'name' => 'BSc Computer Science',
            'code' => 'BCS',
            'capacity' => 50,
            'is_active' => true,
        ]);
    }

    public function test_student_cannot_be_approved_without_payment(): void
    {
        $application = Application::create([
            'application_number' => 'APP-STU-0001',
            'type' => 'student',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.test',
            'phone' => '+1555123456',
            'date_of_birth' => '2002-05-10',
            'gender' => 'female',
            'address' => '100 University Rd',
            'program_id' => $this->program->id,
            'program' => $this->program->name,
            'documents_verified_at' => now(),
            'payment_status' => 'pending',
            'payment_verified_at' => null,
            'status' => 'pending',
        ]);

        // Attempt approve without force
        $response = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.approve', $application->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('payment', strtolower(session('error')));
        $this->assertEquals('pending', $application->fresh()->status);

        // Attempt approve WITH force_approve: strictly must still fail
        $forceResponse = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.approve', $application->id), [
            'force_approve' => true,
        ]);
        $forceResponse->assertSessionHas('error');
        $this->assertStringContainsString('payment', strtolower(session('error')));
        $this->assertEquals('pending', $application->fresh()->status);
    }

    public function test_student_cannot_be_approved_with_uploaded_but_unverified_payment(): void
    {
        $application = Application::create([
            'application_number' => 'APP-STU-0002',
            'type' => 'student',
            'first_name' => 'Jack',
            'last_name' => 'Smith',
            'email' => 'jack.smith@example.test',
            'phone' => '+1555654321',
            'date_of_birth' => '2001-04-12',
            'gender' => 'male',
            'address' => '200 University Rd',
            'program_id' => $this->program->id,
            'program' => $this->program->name,
            'documents_verified_at' => now(),
            'payment_proof' => 'receipts/proof.jpg',
            'payment_status' => 'uploaded',
            'payment_verified_at' => null,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.approve', $application->id), [
            'force_approve' => true,
        ]);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('payment', strtolower(session('error')));
        $this->assertEquals('pending', $application->fresh()->status);
    }

    public function test_student_cannot_be_approved_without_verified_documents(): void
    {
        $application = Application::create([
            'application_number' => 'APP-STU-0002B',
            'type' => 'student',
            'first_name' => 'Doc',
            'last_name' => 'Check',
            'email' => 'doc.check@example.test',
            'phone' => '+1555654322',
            'date_of_birth' => '2001-04-12',
            'gender' => 'male',
            'address' => '200 University Rd',
            'program_id' => $this->program->id,
            'program' => $this->program->name,
            'documents_verified_at' => null,
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'status' => 'pending',
        ]);

        $this->assertFalse($application->areDocumentsVerified());
        $this->assertFalse($application->canBeAdmitted());

        $response = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.approve', $application->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('document', strtolower(session('error')));
        $this->assertEquals('pending', $application->fresh()->status);
    }

    public function test_student_cannot_be_approved_without_assigned_course(): void
    {
        $application = Application::create([
            'application_number' => 'APP-STU-0003',
            'type' => 'student',
            'first_name' => 'Alice',
            'last_name' => 'Walker',
            'email' => 'alice.walker@example.test',
            'phone' => '+1555987654',
            'date_of_birth' => '2003-01-15',
            'gender' => 'female',
            'address' => '300 Campus Ave',
            'program_choices' => [$this->program->id],
            'program_id' => null,
            'program' => null,
            'documents_verified_at' => now(),
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'status' => 'pending',
        ]);

        // Attempt approve without assigned course
        $response = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.approve', $application->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('programme', strtolower(session('error')));
        $this->assertEquals('pending', $application->fresh()->status);

        // Attempt approve WITH force_approve: strictly must still fail
        $forceResponse = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.approve', $application->id), [
            'force_approve' => true,
        ]);
        $forceResponse->assertSessionHas('error');
        $this->assertStringContainsString('programme', strtolower(session('error')));
        $this->assertEquals('pending', $application->fresh()->status);
    }

    public function test_strict_sequential_wizard_order_enforced(): void
    {
        $application = Application::create([
            'application_number' => 'APP-WIZ-0001',
            'documents' => ['applications/test/transcript.pdf'],
            'type' => 'student',
            'first_name' => 'Order',
            'last_name' => 'Test',
            'email' => 'order.test@example.test',
            'phone' => '+1555999888',
            'date_of_birth' => '2001-08-10',
            'gender' => 'male',
            'address' => '77 Wizard Lane',
            'program_choices' => [$this->program->id],
            'program_id' => null,
            'program' => null,
            'payment_status' => 'uploaded',
            'payment_proof' => 'receipts/test.pdf',
            'payment_verified_at' => null,
            'documents_verified_at' => null,
            'status' => 'pending',
        ]);

        // 1. Cannot verify documents before Step 1 (Payment) is approved
        $failDoc = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.verify-documents', $application->id));
        $failDoc->assertSessionHas('error');
        $this->assertStringContainsString('payment', strtolower(session('error')));

        // 2. Cannot assign program before Step 1 and Step 2 are completed
        $failProg = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.update-program', $application->id), [
            'program_id' => $this->program->id,
        ]);
        $failProg->assertSessionHas('error');

        // 3. Complete Step 1: Verify Payment
        $payResponse = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.verify-payment', $application->id));
        $payResponse->assertSessionHas('success');
        $application->refresh();
        $this->assertTrue($application->isPaymentApproved());

        // 4. Cannot assign program before Step 2 (Documents) is verified
        $failProg2 = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.update-program', $application->id), [
            'program_id' => $this->program->id,
        ]);
        $failProg2->assertSessionHas('error');
        $this->assertStringContainsString('document', strtolower(session('error')));

        // 5. Complete Step 2: Verify Documents
        $docResponse = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.verify-documents', $application->id), [
            'notes' => 'Transcripts and ID verified against bio data.',
        ]);
        $docResponse->assertSessionHas('success');
        $application->refresh();
        $this->assertTrue($application->areDocumentsVerified());

        // 6. Complete Step 3: Assign Programme
        $assignResponse = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.update-program', $application->id), [
            'program_id' => $this->program->id,
        ]);
        $assignResponse->assertSessionHas('success');
        $application->refresh();
        $this->assertTrue($application->hasAssignedCourse());

        // 7. Complete Step 4: Confirm Admission
        $this->assertTrue($application->canBeAdmitted());
        $admitResponse = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.approve', $application->id));
        $admitResponse->assertRedirect(route('admin.applications.index'));
        $admitResponse->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('admitted', $application->status);
        $this->assertNotNull($application->student_number);
    }

    public function test_student_can_be_approved_and_admitted_when_all_steps_complete(): void
    {
        $application = Application::create([
            'application_number' => 'APP-STU-0004',
            'type' => 'student',
            'first_name' => 'David',
            'last_name' => 'Brown',
            'email' => 'david.brown@example.test',
            'phone' => '+1555888999',
            'date_of_birth' => '2000-09-09',
            'gender' => 'male',
            'address' => '400 Scholar Way',
            'previous_school' => 'High School Excellence',
            'previous_qualification' => 'A-Levels',
            'documents' => ['doc1' => 'sample.pdf'],
            'documents_verified_at' => now(),
            'program_choices' => [$this->program->id],
            'program_id' => $this->program->id,
            'program' => $this->program->name,
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'status' => 'pending',
        ]);

        $this->assertTrue($application->hasAssignedCourse());
        $this->assertTrue($application->isPaymentApproved());
        $this->assertTrue($application->areDocumentsVerified());
        $this->assertTrue($application->canBeAdmitted());

        $response = $this->actingAs($this->admin)->from('/admin/applications/' . $application->id)->post(route('admin.applications.approve', $application->id));
        $response->assertRedirect(route('admin.applications.index'));
        $response->assertSessionHas('success');

        $fresh = $application->fresh();
        $this->assertEquals('admitted', $fresh->status);
        $this->assertNotNull($fresh->student_number);
    }

    public function test_bulk_approval_strictly_respects_payment_and_course_rules(): void
    {
        // App 1: Ready (Course assigned + payment verified + documents verified)
        $readyApp = Application::create([
            'application_number' => 'APP-BULK-01',
            'type' => 'student',
            'first_name' => 'Ready',
            'last_name' => 'Student',
            'email' => 'ready@example.test',
            'phone' => '+1555111001',
            'date_of_birth' => '2001-01-01',
            'gender' => 'male',
            'address' => 'Ready Rd',
            'program_id' => $this->program->id,
            'program' => $this->program->name,
            'documents_verified_at' => now(),
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'status' => 'pending',
        ]);

        // App 2: No payment
        $unpaidApp = Application::create([
            'application_number' => 'APP-BULK-02',
            'type' => 'student',
            'first_name' => 'Unpaid',
            'last_name' => 'Student',
            'email' => 'unpaid@example.test',
            'phone' => '+1555111002',
            'date_of_birth' => '2001-01-02',
            'gender' => 'female',
            'address' => 'Unpaid Rd',
            'program_id' => $this->program->id,
            'program' => $this->program->name,
            'documents_verified_at' => now(),
            'payment_status' => 'pending',
            'payment_verified_at' => null,
            'status' => 'pending',
        ]);

        // App 3: No course
        $noCourseApp = Application::create([
            'application_number' => 'APP-BULK-03',
            'type' => 'student',
            'first_name' => 'NoCourse',
            'last_name' => 'Student',
            'email' => 'nocourse@example.test',
            'phone' => '+1555111003',
            'date_of_birth' => '2001-01-03',
            'gender' => 'male',
            'address' => 'NoCourse Rd',
            'program_id' => null,
            'program' => null,
            'documents_verified_at' => now(),
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'status' => 'pending',
        ]);

        // Attempt bulk approve all three with only_ready = true
        $response = $this->actingAs($this->admin)->post(route('admin.applications.bulk-approve'), [
            'application_ids' => [$readyApp->id, $unpaidApp->id, $noCourseApp->id],
            'only_ready' => true,
            'force_approve' => false,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('admitted', $readyApp->fresh()->status);
        $this->assertEquals('pending', $unpaidApp->fresh()->status);
        $this->assertEquals('pending', $noCourseApp->fresh()->status);

        // Attempt bulk approve unpaid and noCourse WITH force_approve = true and only_ready = false
        $forceResponse = $this->actingAs($this->admin)->post(route('admin.applications.bulk-approve'), [
            'application_ids' => [$unpaidApp->id, $noCourseApp->id],
            'only_ready' => false,
            'force_approve' => true,
        ]);

        // Unpaid and noCourse must NOT be admitted
        $this->assertEquals('pending', $unpaidApp->fresh()->status);
        $this->assertEquals('pending', $noCourseApp->fresh()->status);
    }
}
