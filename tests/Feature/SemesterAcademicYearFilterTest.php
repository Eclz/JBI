<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemesterAcademicYearFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_academic_years_do_not_appear_when_creating_new_semester(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $activeYear = AcademicYear::create([
            'name' => 'Active Academic Year 2026/2027',
            'year' => 2026,
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
            'is_active' => true,
        ]);

        $inactiveYear = AcademicYear::create([
            'name' => 'Inactive Academic Year 2024/2025',
            'year' => 2024,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_current' => false,
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.semesters.create'));

        $response->assertOk();
        $response->assertSee('Active Academic Year 2026/2027');
        $response->assertDontSee('Inactive Academic Year 2024/2025');

        $viewAcademicYears = $response->viewData('academicYears');
        $this->assertTrue($viewAcademicYears->contains('id', $activeYear->id));
        $this->assertFalse($viewAcademicYears->contains('id', $inactiveYear->id));
    }

    public function test_cannot_create_semester_with_inactive_academic_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $inactiveYear = AcademicYear::create([
            'name' => 'Inactive Academic Year 2024/2025',
            'year' => 2024,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_current' => false,
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.semesters.store'), [
            'academic_year_id' => $inactiveYear->id,
            'name' => 'Semester 1',
            'start_date' => '2024-09-01',
            'end_date' => '2025-01-31',
            'registration_start' => '2024-08-01',
            'registration_end' => '2024-08-31',
        ]);

        $response->assertSessionHasErrors(['academic_year_id']);
        $this->assertDatabaseMissing('semesters', [
            'name' => 'Semester 1',
            'academic_year_id' => $inactiveYear->id,
        ]);
    }

    public function test_can_create_semester_with_active_academic_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $activeYear = AcademicYear::create([
            'name' => 'Active Academic Year 2026/2027',
            'year' => 2026,
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.semesters.store'), [
            'academic_year_id' => $activeYear->id,
            'name' => 'Semester 1',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-31',
            'registration_start' => '2026-08-01',
            'registration_end' => '2026-08-31',
        ]);

        $response->assertRedirect(route('admin.semesters.index'));
        $this->assertDatabaseHas('semesters', [
            'name' => 'Semester 1',
            'academic_year_id' => $activeYear->id,
        ]);
    }
}
