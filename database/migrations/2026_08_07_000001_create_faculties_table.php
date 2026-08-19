<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'school_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }

        Schema::dropIfExists('schools');
        Schema::dropIfExists('faculties');

        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('dean_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        if (Schema::hasTable('departments') && !Schema::hasColumn('departments', 'faculty_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->foreignId('faculty_id')->nullable()->after('id')->constrained('faculties')->onDelete('set null');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'faculty_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropColumn('faculty_id');
            });
        }

        Schema::dropIfExists('faculties');

        Schema::enableForeignKeyConstraints();
    }
};
