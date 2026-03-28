<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stripe_checkout_sessions', function (Blueprint $table) {
            $table->unsignedInteger('package_coupon_id')->nullable()->after('package_id');
            $table->decimal('coupon_discount_amount', 10, 2)->nullable()->after('package_coupon_id');
            $table->unsignedInteger('original_amount_cents')->nullable()->after('coupon_discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('stripe_checkout_sessions', function (Blueprint $table) {
            $table->dropColumn(['package_coupon_id', 'coupon_discount_amount', 'original_amount_cents']);
        });
    }
};
