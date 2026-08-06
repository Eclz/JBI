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
        Schema::create('schools', function (Blueprint $table) {
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

        // Add school_id to departments if not exists
        if (Schema::hasTable('departments') && !Schema::hasColumn('departments', 'school_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'school_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }

        Schema::dropIfExists('schools');
    }
};
