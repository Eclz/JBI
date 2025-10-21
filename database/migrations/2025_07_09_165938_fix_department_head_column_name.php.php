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
        Schema::table('departments', function (Blueprint $table) {
            // Check if the old column exists and rename it
            if (Schema::hasColumn('departments', 'head_id')) {
                $table->renameColumn('head_id', 'head_of_department_id');
            } else if (!Schema::hasColumn('departments', 'head_of_department_id')) {
                // If neither exists, create the new column
                $table->foreignId('head_of_department_id')->nullable()->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'head_of_department_id')) {
                $table->renameColumn('head_of_department_id', 'head_id');
            }
        });
    }
};
