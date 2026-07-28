<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('course_fees')) {
            Schema::table('course_fees', function (Blueprint $table) {
                if (!Schema::hasColumn('course_fees', 'exam_fee')) {
                    $table->decimal('exam_fee', 10, 2)->default(0)->after('amount');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('course_fees')) {
            Schema::table('course_fees', function (Blueprint $table) {
                if (Schema::hasColumn('course_fees', 'exam_fee')) {
                    $table->dropColumn('exam_fee');
                }
            });
        }
    }
};
