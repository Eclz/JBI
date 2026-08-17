<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable()->after('payment_ref');
                $table->string('stripe_charge_id')->nullable()->after('stripe_payment_intent_id');
                $table->string('card_brand')->nullable()->after('stripe_charge_id');
                $table->string('card_last4')->nullable()->after('card_brand');
                $table->string('stripe_receipt_url')->nullable()->after('card_last4');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable();
                $table->string('stripe_charge_id')->nullable();
                $table->string('card_brand')->nullable();
                $table->string('card_last4')->nullable();
                $table->string('stripe_receipt_url')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['stripe_payment_intent_id', 'stripe_charge_id', 'card_brand', 'card_last4', 'stripe_receipt_url']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['stripe_payment_intent_id', 'stripe_charge_id', 'card_brand', 'card_last4', 'stripe_receipt_url']);
        });
    }
};
