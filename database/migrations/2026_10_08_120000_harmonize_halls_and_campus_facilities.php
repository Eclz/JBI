<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateHallNames = DB::table('hostels')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('name');

        if ($duplicateHallNames->isNotEmpty()) {
            throw new \RuntimeException(
                'Cannot harmonize halls because multiple halls share these names: ' . $duplicateHallNames->implode(', ')
            );
        }

        Schema::table('campus_facilities', function (Blueprint $table) {
            $table->string('type', 30)->default('facility')->after('id');
            $table->string('hall_type', 20)->nullable()->after('type');
            $table->unsignedInteger('capacity')->nullable()->after('hall_type');
            $table->foreignId('dean_id')->nullable()->after('capacity')
                ->constrained('users')->nullOnDelete();
        });

        Schema::table('campus_facilities', function (Blueprint $table) {
            $table->dropUnique('campus_facilities_name_unique');
            $table->unique(['type', 'name']);
        });

        Schema::table('hostel_rooms', function (Blueprint $table) {
            $table->foreignId('campus_facility_id')->nullable()->after('hostel_id')
                ->constrained('campus_facilities')->nullOnDelete();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreignId('campus_facility_id')->nullable()->after('hostel_id')
                ->constrained('campus_facilities')->nullOnDelete();
        });

        $hostelFacilityIds = [];
        foreach (DB::table('hostels')->orderBy('id')->get() as $hostel) {
            $facilityId = DB::table('campus_facilities')->insertGetId([
                'type' => 'hall',
                'hall_type' => $hostel->type,
                'name' => $hostel->name,
                'location' => $hostel->location,
                'description' => $hostel->description,
                'capacity' => $hostel->capacity,
                'dean_id' => $hostel->dean_id,
                'is_active' => $hostel->is_active,
                'created_at' => $hostel->created_at,
                'updated_at' => $hostel->updated_at,
            ]);

            $hostelFacilityIds[$hostel->id] = $facilityId;
        }

        foreach ($hostelFacilityIds as $hostelId => $facilityId) {
            DB::table('hostel_rooms')->where('hostel_id', $hostelId)
                ->update(['campus_facility_id' => $facilityId]);
            DB::table('student_profiles')->where('hostel_id', $hostelId)
                ->update(['campus_facility_id' => $facilityId]);
        }

        foreach (DB::table('facility_rooms')->whereNull('campus_facility_id')->whereNotNull('building')->get() as $room) {
            $facilityId = DB::table('campus_facilities')
                ->where('name', $room->building)
                ->value('id');

            if ($facilityId) {
                DB::table('facility_rooms')->where('id', $room->id)
                    ->update(['campus_facility_id' => $facilityId]);
            }
        }

        Schema::table('hostel_rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hostel_id');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hostel_id');
        });

        Schema::drop('hostels');
    }

    public function down(): void
    {
        $duplicateNames = DB::table('campus_facilities')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('name');

        if ($duplicateNames->isNotEmpty()) {
            throw new \RuntimeException(
                'Cannot restore the separate halls table while facility names overlap across types: ' . $duplicateNames->implode(', ')
            );
        }

        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['male', 'female', 'mixed']);
            $table->string('location')->nullable();
            $table->integer('capacity')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('dean_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('hostel_rooms', function (Blueprint $table) {
            $table->foreignId('hostel_id')->nullable()->after('id')
                ->constrained('hostels')->nullOnDelete();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreignId('hostel_id')->nullable()->after('id')
                ->constrained('hostels')->nullOnDelete();
        });

        $hallIds = [];
        foreach (DB::table('campus_facilities')->where('type', 'hall')->orderBy('id')->get() as $hall) {
            $hostelId = DB::table('hostels')->insertGetId([
                'name' => $hall->name,
                'type' => $hall->hall_type,
                'location' => $hall->location,
                'capacity' => $hall->capacity,
                'description' => $hall->description,
                'is_active' => $hall->is_active,
                'dean_id' => $hall->dean_id,
                'created_at' => $hall->created_at,
                'updated_at' => $hall->updated_at,
            ]);

            $hallIds[$hall->id] = $hostelId;
        }

        foreach ($hallIds as $facilityId => $hostelId) {
            DB::table('hostel_rooms')->where('campus_facility_id', $facilityId)
                ->update(['hostel_id' => $hostelId]);
            DB::table('student_profiles')->where('campus_facility_id', $facilityId)
                ->update(['hostel_id' => $hostelId]);
        }

        Schema::table('hostel_rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_facility_id');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_facility_id');
        });

        Schema::table('campus_facilities', function (Blueprint $table) {
            $table->dropUnique(['type', 'name']);
            $table->unique('name');
            $table->dropConstrainedForeignId('dean_id');
            $table->dropColumn(['type', 'hall_type', 'capacity']);
        });
    }
};
