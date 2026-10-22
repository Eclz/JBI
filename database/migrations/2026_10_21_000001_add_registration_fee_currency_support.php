<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('payment_currency', 3)->default('ZAR')->after('payment_status');
        });

        Schema::table('fee_records', function (Blueprint $table) {
            $table->string('currency', 3)->default('ZAR')->after('amount');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency', 3)->default('ZAR')->after('amount');
        });

        Schema::table('finance_revenues', function (Blueprint $table) {
            $table->string('currency', 3)->default('ZAR')->after('amount');
        });

        DB::table('fee_structures')
            ->whereIn('name', ['Admission & Application Fee', 'Admission Fee', 'Registration Fee'])
            ->update([
                'name' => 'Registration Fee',
                'amount' => 500,
                'currency' => 'ZAR',
                'description' => 'Non-refundable student registration fee: ZAR 500 or USD 35.',
            ]);
    }

    public function down(): void
    {
        Schema::table('finance_revenues', fn (Blueprint $table) => $table->dropColumn('currency'));
        Schema::table('payments', fn (Blueprint $table) => $table->dropColumn('currency'));
        Schema::table('fee_records', fn (Blueprint $table) => $table->dropColumn('currency'));
        Schema::table('applications', fn (Blueprint $table) => $table->dropColumn('payment_currency'));
    }
};
