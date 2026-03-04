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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable()->index('company_id')->comment('Company ID (null for jobseeker transactions)');
            $table->unsignedInteger('user_id')->nullable()->index('idx_user_id')->comment('Jobseeker user ID (null for company transactions)');
            $table->enum('user_type', ['company', 'jobseeker'])->default('company')->index('idx_user_type')->comment('Type of user who made the payment');
            $table->integer('package_id')->index('package_id');
            $table->enum('package_type', ['job', 'cv_search', 'featured_profile', 'job_seeker'])->default('job')->comment('Type of package purchased');
            $table->string('package_title')->nullable();
            $table->decimal('package_price', 10);
            $table->string('payment_method', 50)->nullable();
            $table->unsignedInteger('assigned_by')->nullable()->index('idx_assigned_by')->comment('Admin user ID who assigned the package (null if purchased)');
            $table->string('transaction_id')->nullable();
            $table->timestamp('package_start_date')->nullable();
            $table->timestamp('package_end_date')->nullable();
            $table->integer('jobs_quota')->nullable();
            $table->integer('cvs_quota')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->nullable()->default('completed')->index('payment_status');
            $table->timestamp('created_at')->useCurrent()->index('created_at');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->index(['package_type', 'created_at'], 'idx_package_type_created');
            $table->index(['payment_status', 'created_at'], 'idx_payment_status_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_history');
    }
};
