<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_coupon_redemptions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_coupon_id');
            $table->unsignedInteger('package_id');
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('currency', 8)->default('USD');
            $table->string('stripe_checkout_session_id', 255)->nullable()->unique();
            $table->string('stripe_charge_id', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('package_coupon_id')->references('id')->on('package_coupons')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_coupon_redemptions');
    }
};
