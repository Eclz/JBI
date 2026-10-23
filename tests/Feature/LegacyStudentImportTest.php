<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LegacyStudentImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_registrar_can_import_legacy_student_records_and_get_a_row_report(): void
    {
        $registrarRole = Role::where('slug', 'registrar')->firstOrFail();
        $this->assertTrue($registrarRole->hasPermission('students', 'import'));
        $registrar = User::factory()->create([
            'role' => 'admin',
            'role_id' => $registrarRole->id,
        ]);
        $department = Department::factory()->active()->create([
            'name' => 'Theology',
            'code' => 'THE',
        ]);
        $csv = implode("\n", [
            'first_name,last_name,email,admission_number,admission_date,department,program,status,current_semester,year_of_study,phone,date_of_birth,gender,address',
            'Mary,Legacy,mary.legacy@example.test,OLD/2014/0001,2014-09-01,THE,Legacy Theology Program,active,2,1,+254700000001,1995-04-12,female,Old campus address',
            'Duplicate,Email,mary.legacy@example.test,OLD/2014/0002,2014-09-01,THE,Legacy Theology Program,active,,,,,,',
            'Missing,Department,missing.department@example.test,OLD/2014/0003,2014-09-01,Unknown,Legacy Theology Program,active,,,,,,',
        ]);

        $this->actingAs($registrar)
            ->post(route('admin.students.bulk-import'), [
                'file' => UploadedFile::fake()->createWithContent('legacy-students.csv', $csv),
            ])
            ->assertRedirect()
            ->assertSessionHas('import_report', function (array $report): bool {
                return $report['created'] === 1
                    && $report['skipped'] === 1
                    && $report['errors'] === 1
                    && $report['row_errors'][0]['row'] === 4;
            });

        $student = User::where('email', 'mary.legacy@example.test')->firstOrFail();
        $this->assertSame('student', $student->role);
        $this->assertTrue($student->must_change_password);
        $this->assertDatabaseHas('student_profiles', [
            'user_id' => $student->id,
            'admission_number' => 'OLD/2014/0001',
            'department_id' => $department->id,
            'program' => 'Legacy Theology Program',
            'status' => 'active',
            'application_status' => 'approved',
        ]);
        $this->assertSame('2014-09-01', $student->studentProfile->admission_date->format('Y-m-d'));
        $this->assertSame('1995-04-12', $student->fresh()->date_of_birth->format('Y-m-d'));
    }

    public function test_registrar_can_upload_student_documents_while_editing_a_legacy_record(): void
    {
        Storage::fake('local');
        $registrarRole = Role::where('slug', 'registrar')->firstOrFail();
        $this->assertTrue($registrarRole->hasPermission('students', 'edit'));
        $registrar = User::factory()->create([
            'role' => 'admin',
            'role_id' => $registrarRole->id,
        ]);
        $studentRole = Role::where('slug', 'student')->firstOrFail();
        $department = Department::factory()->active()->create();
        $student = User::factory()->student()->create([
            'role_id' => $studentRole->id,
            'first_name' => 'Legacy',
            'last_name' => 'Student',
        ]);
        $student->studentProfile()->create([
            'admission_number' => 'OLD/2014/0001',
            'admission_date' => '2014-09-01',
            'department_id' => $department->id,
            'program' => 'Legacy program with no mapped record',
            'status' => 'active',
            'documents' => ['legacy/previous-document.pdf'],
        ]);

        $this->actingAs($registrar)
            ->get(route('admin.students.edit', $student))
            ->assertOk()
            ->assertSee('previous-document.pdf');

        $this->actingAs($registrar)
            ->put(route('admin.students.update', $student), [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'email' => $student->email,
                'admission_number' => 'OLD/2014/0001',
                'admission_date' => '2014-09-01',
                'department_id' => $department->id,
                'program_id' => '',
                'status' => 'active',
                'documents' => [
                    UploadedFile::fake()->create('legacy-record.pdf', 10, 'application/pdf'),
                ],
            ])
            ->assertRedirect(route('admin.students.show', $student))
            ->assertSessionHas('success');

        $student->refresh()->load('studentProfile');
        $documents = $student->studentProfile->documents;
        $document = $documents[1];
        $this->assertSame('legacy-record.pdf', $document['name']);
        Storage::disk('local')->assertExists($document['path']);
        $this->assertSame('legacy/previous-document.pdf', $documents[0]);
        $this->assertSame('OLD/2014/0001', $student->studentProfile->admission_number);
        $this->assertSame('Legacy program with no mapped record', $student->studentProfile->program);

        $this->actingAs($registrar)
            ->get(route('admin.students.documents.download', [$student, 1]))
            ->assertOk()
            ->assertDownload('legacy-record.pdf');
    }

    public function test_only_roles_with_student_import_permission_can_download_the_template_or_import(): void
    {
        $lecturerRole = Role::where('slug', 'lecturer')->firstOrFail();
        $lecturer = User::factory()->create([
            'role' => 'faculty',
            'role_id' => $lecturerRole->id,
        ]);

        $this->actingAs($lecturer)
            ->get(route('admin.students.import-template'))
            ->assertForbidden();
    }

    public function test_import_button_and_guide_are_available_to_registrar(): void
    {
        $registrar = User::factory()->create([
            'role' => 'admin',
            'role_id' => Role::where('slug', 'registrar')->firstOrFail()->id,
        ]);

        Department::factory()->active()->create();

        $this->actingAs($registrar)
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee('Import Students')
            ->assertSee('Download the Excel template')
            ->assertSee('Do not add file paths or attachments to the spreadsheet');
    }

    public function test_default_super_administrator_can_import_existing_students(): void
    {
        $role = Role::where('slug', 'super_administrator')->firstOrFail();
        $user = User::factory()->create([
            'role' => 'admin',
            'role_id' => $role->id,
        ]);

        $this->assertTrue($user->hasPermission('students', 'import'));
        $this->actingAs($user)
            ->get(route('admin.students.import-template'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
