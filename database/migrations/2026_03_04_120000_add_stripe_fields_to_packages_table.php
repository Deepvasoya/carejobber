<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * type: one_time_credits = pay-per-post package, monthly_recurring = subscription
     * country_code: ISO 2-letter (e.g. CA, US) or null for all
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('type', 30)->nullable()->default('one_time_credits')->after('package_for')
                ->comment('one_time_credits|monthly_recurring|resume_boost');
            $table->string('stripe_price_id', 255)->nullable()->after('package_price');
            $table->string('country_code', 2)->nullable()->after('stripe_price_id')->comment('ISO 2-letter or null = all');
            $table->unsignedTinyInteger('rebate_percent')->nullable()->after('country_code');
            $table->boolean('is_active')->default(true)->after('rebate_percent');
            $table->unsignedSmallInteger('duration_days')->nullable()->after('package_num_listings')->comment('For subscriptions: plan length in days');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['type', 'stripe_price_id', 'country_code', 'rebate_percent', 'is_active', 'duration_days']);
        });
    }
};
