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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('availed_cvs_quota')->index();
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id')->index();
            $table->string('stripe_subscription_status')->nullable()->after('stripe_subscription_id')->comment('active, past_due, canceled, etc.');
            $table->timestamp('stripe_subscription_ends_at')->nullable()->after('stripe_subscription_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'stripe_subscription_id', 'stripe_subscription_status', 'stripe_subscription_ends_at']);
        });
    }
};
