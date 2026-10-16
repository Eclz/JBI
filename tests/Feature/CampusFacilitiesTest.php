<?php

namespace Tests\Feature;

use App\Models\CampusFacility;
use App\Models\FacilityRoom;
use App\Models\HostelRoom;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampusFacilitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_create_and_rename_a_facility_without_losing_linked_rooms(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => null]);
        $this->actingAs($admin);

        $this->post(route('facilities.buildings.store'), [
            'name' => 'Main Library',
            'location' => 'North Campus',
            'description' => 'Library and study services',
        ])->assertRedirect(route('facilities.buildings.index'));

        $facility = CampusFacility::where('name', 'Main Library')->firstOrFail();
        $this->get(route('facilities.buildings.index'))->assertOk()->assertSee('Main Library');
        $this->get(route('facilities.buildings.edit', $facility))->assertOk()->assertSee('Facility name');

        $room = FacilityRoom::create([
            'name' => 'Study Room 1',
            'campus_facility_id' => $facility->id,
            'building' => 'East Wing',
            'room_type' => 'Study',
            'capacity' => 12,
            'status' => 'Available',
        ]);

        $this->put(route('facilities.buildings.update', $facility), [
            'name' => 'JBI Main Library',
            'location' => 'Central Campus',
            'description' => 'Updated facility details',
            'is_active' => '1',
        ])->assertRedirect(route('facilities.buildings.index'));

        $this->assertDatabaseHas('campus_facilities', [
            'id' => $facility->id,
            'name' => 'JBI Main Library',
            'location' => 'Central Campus',
        ]);
        $this->assertDatabaseHas('facility_rooms', [
            'id' => $room->id,
            'campus_facility_id' => $facility->id,
            'building' => 'East Wing',
        ]);

        $this->get(route('facilities.rooms.create'))->assertOk()->assertSee('JBI Main Library');
    }

    public function test_facility_creation_requires_the_facilities_create_permission(): void
    {
        $role = Role::create([
            'name' => 'Read-only admin',
            'slug' => 'read_only_admin',
            'guard_role' => 'admin',
            'permissions' => ['facilities' => ['view' => true]],
            'is_active' => true,
        ]);
        $user = User::factory()->create(['role' => 'admin', 'role_id' => $role->id]);

        $this->actingAs($user)
            ->post(route('facilities.buildings.store'), ['name' => 'Restricted Facility'])
            ->assertForbidden();

        $this->assertDatabaseMissing('campus_facilities', ['name' => 'Restricted Facility']);
    }

    public function test_halls_are_facilities_of_hall_type_and_keep_their_accommodation_rooms(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => null]);
        $this->actingAs($admin);

        $this->post(route('facilities.buildings.store'), [
            'name' => 'Main Library',
            'location' => 'North Campus',
        ])->assertRedirect(route('facilities.buildings.index'));

        $this->assertSame('facility', CampusFacility::where('name', 'Main Library')->firstOrFail()->type);

        $this->post(route('admin.hostel.storeHostel'), [
            'name' => 'Cedar Hall',
            'type' => 'mixed',
            'capacity' => 120,
            'location' => 'West Campus',
            'description' => 'Student residence',
        ])->assertRedirect()->assertSessionHasNoErrors()->assertSessionHas('success', 'Hall of Residence created successfully.');

        $hall = CampusFacility::where('name', 'Cedar Hall')->firstOrFail();
        $this->assertSame('hall', $hall->type);
        $this->assertSame('mixed', $hall->hall_type);
        $this->assertSame(120, $hall->capacity);
        $this->get(route('admin.hostel.edit', $hall))->assertOk()->assertSee('Edit Hall of Residence');
        $this->get(route('facilities.buildings.index'))->assertOk()->assertSee('Hall of Residence')->assertSee('Cedar Hall');

        $this->post(route('admin.hostel.storeRoom', $hall), [
            'room_number' => 'C-101',
            'capacity' => 2,
            'fee_per_semester' => 1500,
        ])->assertRedirect();

        $room = HostelRoom::where('campus_facility_id', $hall->id)->firstOrFail();
        $this->assertSame($hall->id, $room->hostel->id);

        $this->put(route('admin.hostel.update', $hall), [
            'name' => 'Cedar Residence',
            'type' => 'female',
            'capacity' => 125,
            'location' => 'West Campus',
            'description' => 'Updated residence details',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertSame('Cedar Residence', $room->fresh()->hostel->name);
        $this->assertSame('facility', CampusFacility::where('name', 'Main Library')->firstOrFail()->type);
    }
}
