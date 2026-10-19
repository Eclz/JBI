<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\CampusFacility;
use App\Models\HostelRoom;
use App\Models\HostelAllocation;
use App\Models\FeeRecord;
use App\Models\Payment;
use App\Models\FinanceRevenue;
use App\Models\Notification;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Course;
use App\Models\StudentProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HostelPaidRoomInvoiceFinanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected CampusFacility $hall;
    protected HostelRoom $paidRoom;
    protected HostelRoom $freeRoom;
    protected Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup academic year and semester
        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'year' => '2026/2027',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
            'is_current' => true,
        ]);

        $this->semester = Semester::create([
            'name' => 'Semester 1 2026/2027',
            'academic_year_id' => $academicYear->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(3),
            'is_current' => true,
            'is_active' => true,
        ]);

        // Create Admin user with halls and registrar permissions
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $adminRole = Role::firstOrCreate(['slug' => 'super_administrator'], [
            'name' => 'Super Administrator',
            'permissions' => [
                'halls_of_residence' => [
                    'view' => true,
                    'create' => true,
                    'edit' => true,
                    'approve' => true,
                ],
                'registrar_hub' => [
                    'view' => true,
                    'edit' => true,
                ],
            ],
        ]);
        $this->admin->assignRole($adminRole, true);

        // Create Student user
        $this->student = User::factory()->create([
            'role' => 'student',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@test.com',
            'is_active' => true,
        ]);

        // Create Hall of Residence
        $this->hall = CampusFacility::create([
            'type' => 'hall',
            'hall_type' => 'mixed',
            'name' => 'Lumumba Hall',
            'capacity' => 100,
            'location' => 'East Campus',
            'is_active' => true,
        ]);

        // Create Paid Room ($450) and Free Room ($0)
        $this->paidRoom = HostelRoom::create([
            'campus_facility_id' => $this->hall->id,
            'room_number' => 'L-101',
            'capacity' => 2,
            'occupancy' => 0,
            'fee_per_semester' => 450.00,
            'status' => 'available',
        ]);

        $this->freeRoom = HostelRoom::create([
            'campus_facility_id' => $this->hall->id,
            'room_number' => 'L-102',
            'capacity' => 2,
            'occupancy' => 0,
            'fee_per_semester' => 0.00,
            'status' => 'available',
        ]);
    }

    public function test_paid_room_approval_creates_invoice_and_finance_transaction()
    {
        $allocation = HostelAllocation::create([
            'user_id' => $this->student->id,
            'hostel_room_id' => $this->paidRoom->id,
            'semester_id' => $this->semester->id,
            'allocation_date' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.hostel.approveAllocation', $allocation));

        $response->assertSessionHas('success');
        $this->assertEquals('approved', $allocation->fresh()->status);
        $this->assertEquals(1, $this->paidRoom->fresh()->occupancy);

        // 1. Verify FeeRecord (Student Invoice)
        $invoice = FeeRecord::where('user_id', $this->student->id)
            ->where('type', 'accommodation')
            ->first();

        $this->assertNotNull($invoice, 'Invoice FeeRecord should be generated for student');
        $this->assertEquals(450.00, (float) $invoice->amount);
        $this->assertEquals(450.00, (float) $invoice->balance_amount);
        $this->assertEquals('pending', $invoice->status);
        $this->assertStringStartsWith('INV-HALL-', $invoice->invoice_number);

        // 2. Verify Payment (Transaction in Finance)
        $payment = Payment::where('student_id', $this->student->id)
            ->where('fee_record_id', $invoice->id)
            ->first();

        $this->assertNotNull($payment, 'Payment transaction should be recorded in finance');
        $this->assertEquals(450.00, (float) $payment->amount);
        $this->assertEquals($invoice->invoice_number, $payment->reference_number);
        $this->assertEquals('pending', $payment->status);

        // 3. Verify FinanceRevenue stream
        $revenue = FinanceRevenue::where('reference_number', $invoice->invoice_number)->first();
        $this->assertNotNull($revenue, 'Finance revenue record should be created');
        $this->assertEquals('Accommodation & Halls', $revenue->category);
        $this->assertEquals(450.00, (float) $revenue->amount);

        // 4. Verify Student Notification
        $notification = Notification::where('user_id', $this->student->id)
            ->where('type', 'invoice')
            ->first();

        $this->assertNotNull($notification, 'Invoice notification should be sent to student');
        $this->assertStringContainsString('450.00', $notification->message);
    }

    public function test_free_room_approval_does_not_create_fee_records()
    {
        $allocation = HostelAllocation::create([
            'user_id' => $this->student->id,
            'hostel_room_id' => $this->freeRoom->id,
            'semester_id' => $this->semester->id,
            'allocation_date' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.hostel.approveAllocation', $allocation));

        $response->assertSessionHas('success');
        $this->assertEquals('approved', $allocation->fresh()->status);
        $this->assertEquals(1, $this->freeRoom->fresh()->occupancy);

        // No FeeRecord or Payment created
        $this->assertDatabaseMissing('fee_records', [
            'user_id' => $this->student->id,
            'type' => 'accommodation',
        ]);
        $this->assertDatabaseMissing('payments', [
            'student_id' => $this->student->id,
        ]);
    }

    public function test_hall_show_displays_rooms_and_occupancy_status()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.hostel.show', $this->hall));

        $response->assertStatus(200);
        $response->assertSee('Lumumba Hall');
        $response->assertSee('L-101');
        $response->assertSee('L-102');
        $response->assertSee('Bed Capacity');
        $response->assertSee('Add Room to Lumumba Hall');
    }

    public function test_can_add_room_from_hall_show_page()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.hostel.storeRoom', $this->hall), [
                'room_number' => 'L-201',
                'capacity' => 4,
                'fee_per_semester' => 600.00,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('hostel_rooms', [
            'campus_facility_id' => $this->hall->id,
            'room_number' => 'L-201',
            'capacity' => 4,
            'fee_per_semester' => 600.00,
        ]);
    }

    public function test_delete_all_notifications()
    {
        Notification::create([
            'user_id' => $this->student->id,
            'title' => 'Test 1',
            'message' => 'Message 1',
            'type' => 'system',
        ]);
        Notification::create([
            'user_id' => $this->student->id,
            'title' => 'Test 2',
            'message' => 'Message 2',
            'type' => 'system',
        ]);

        $this->assertEquals(2, Notification::where('user_id', $this->student->id)->count());

        $response = $this->actingAs($this->student)
            ->delete(route('notifications.delete-all'));

        $response->assertRedirect();
        $this->assertEquals(0, Notification::where('user_id', $this->student->id)->count());
    }

    public function test_registrar_dashboard_view_link_and_profile_picture()
    {
        $faculty = Faculty::create([
            'name' => 'Faculty of Computing',
            'code' => 'FOC',
            'is_active' => true,
        ]);
        $dept = Department::create([
            'name' => 'Computer Science',
            'code' => 'CS',
            'faculty_id' => $faculty->id,
            'is_active' => true,
        ]);

        $profile = StudentProfile::create([
            'user_id' => $this->student->id,
            'admission_number' => 'ADM-2026-999',
            'admission_date' => now(),
            'department_id' => $dept->id,
            'program' => 'Bachelor of Computer Science',
            'status' => 'active',
        ]);

        $role = Role::firstOrCreate(['slug' => 'registrar'], [
            'name' => 'Registrar',
            'permissions' => [
                'registrar_hub' => ['view' => true],
            ],
        ]);
        $this->admin->assignRole($role);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.registrar.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('ADM-2026-999');
        // Check that view link points to student User id, NOT StudentProfile id
        $expectedUrl = route('admin.students.show', $this->student->id);
        $response->assertSee($expectedUrl);
    }

    public function test_strict_faculty_course_enrollment()
    {
        $faculty1 = Faculty::create(['name' => 'Faculty of Engineering', 'code' => 'FOE', 'is_active' => true]);
        $dept1 = Department::create(['name' => 'Civil Engineering', 'code' => 'CIV', 'faculty_id' => $faculty1->id, 'is_active' => true]);
        
        $faculty2 = Faculty::create(['name' => 'Faculty of Law', 'code' => 'FOL', 'is_active' => true]);
        $dept2 = Department::create(['name' => 'Commercial Law', 'code' => 'LAW', 'faculty_id' => $faculty2->id, 'is_active' => true]);

        // Student belongs to Faculty 2 (Law)
        StudentProfile::create([
            'user_id' => $this->student->id,
            'admission_number' => 'ADM-LAW-001',
            'admission_date' => now(),
            'department_id' => $dept2->id,
            'program' => 'Bachelor of Laws',
            'status' => 'active',
        ]);

        // Course belongs to Faculty 1 (Engineering)
        $course = Course::create([
            'name' => 'Structural Mechanics',
            'code' => 'CIV101',
            'course_code' => 'CIV101',
            'department_id' => $dept1->id,
            'semester_id' => $this->semester->id,
            'credit_units' => 3,
            'max_capacity' => 40,
            'status' => 'active',
        ]);

        // 1. Try to enroll student from Faculty of Law into an Engineering course unit
        $response = $this->actingAs($this->admin)
            ->post(route('admin.enrollments.store'), [
                'student_id' => $this->student->id,
                'course_id' => $course->id,
                'enrollment_date' => now()->toDateString(),
                'status' => 'enrolled',
                'enrollment_type' => 'normal',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('course_enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $course->id,
        ]);

        // 2. Test AJAX faculty students filter returns only students in the course's faculty
        $filterResponse = $this->actingAs($this->admin)
            ->get(route('admin.enrollments.faculty-students', ['course_id' => $course->id]));

        $filterResponse->assertStatus(200);
        $json = $filterResponse->json();
        // Since our student is in Law (not Engineering), student should not be in the returned list
        $studentIds = collect($json['students'])->pluck('id')->all();
        $this->assertNotContains($this->student->id, $studentIds);
    }
}
