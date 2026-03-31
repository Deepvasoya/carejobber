<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_coupons', function (Blueprint $table) {
            $table->json('resume_promotion_package_ids')->nullable()->after('package_ids');
        });

        Schema::table('package_coupon_redemptions', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        DB::statement('ALTER TABLE `package_coupon_redemptions` MODIFY `package_id` INT UNSIGNED NULL');

        Schema::table('package_coupon_redemptions', function (Blueprint $table) {
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->unsignedBigInteger('resume_promotion_package_id')->nullable()->after('package_id');
            $table->foreign('resume_promotion_package_id')->references('id')->on('resume_promotion_packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('package_coupon_redemptions', function (Blueprint $table) {
            $table->dropForeign(['resume_promotion_package_id']);
            $table->dropColumn('resume_promotion_package_id');
        });

        Schema::table('package_coupons', function (Blueprint $table) {
            $table->dropColumn('resume_promotion_package_ids');
        });
    }
};
