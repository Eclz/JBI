<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('learning_progress')) {
            return;
        }

        Schema::table('learning_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('learning_progress', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('content_id');
            }
            if (!Schema::hasColumn('learning_progress', 'video_watched_seconds')) {
                $table->unsignedInteger('video_watched_seconds')->default(0)->after('read_at');
            }
            if (!Schema::hasColumn('learning_progress', 'video_duration_seconds')) {
                $table->unsignedInteger('video_duration_seconds')->default(0)->after('video_watched_seconds');
            }
            if (!Schema::hasColumn('learning_progress', 'last_video_position_seconds')) {
                $table->unsignedInteger('last_video_position_seconds')->default(0)->after('video_duration_seconds');
            }
            if (!Schema::hasColumn('learning_progress', 'is_video_completed')) {
                $table->boolean('is_video_completed')->default(false)->after('last_video_position_seconds');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('learning_progress')) {
            return;
        }

        Schema::table('learning_progress', function (Blueprint $table) {
            $columnsToDrop = array_filter([
                Schema::hasColumn('learning_progress', 'read_at') ? 'read_at' : null,
                Schema::hasColumn('learning_progress', 'video_watched_seconds') ? 'video_watched_seconds' : null,
                Schema::hasColumn('learning_progress', 'video_duration_seconds') ? 'video_duration_seconds' : null,
                Schema::hasColumn('learning_progress', 'last_video_position_seconds') ? 'last_video_position_seconds' : null,
                Schema::hasColumn('learning_progress', 'is_video_completed') ? 'is_video_completed' : null,
            ]);

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
