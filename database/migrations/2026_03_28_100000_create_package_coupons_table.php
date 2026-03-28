<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 64)->unique();
            $table->string('admin_note', 255)->nullable();
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('max_discount_amount', 10, 2)->nullable()->comment('Cap when discount_type is percent');
            $table->decimal('min_package_price', 10, 2)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('usage_limit_total')->nullable();
            $table->unsignedInteger('usage_limit_per_buyer')->nullable();
            $table->string('package_for_scope', 32)->nullable()->comment('null = any package_for');
            $table->json('package_ids')->nullable()->comment('null or empty = all packages in scope');
            $table->boolean('allow_subscription_packages')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_coupons');
    }
};
