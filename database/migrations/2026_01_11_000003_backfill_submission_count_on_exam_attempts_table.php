<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('exam_attempts')
            ->whereNotNull('submitted_at')
            ->where('submission_count', 0)
            ->update(['submission_count' => 1]);
    }

    public function down(): void
    {
        // No-op to avoid reducing valid counts.
    }
};
