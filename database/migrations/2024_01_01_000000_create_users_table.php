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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('name', 250)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->string('father_name', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('gender_id')->nullable();
            $table->integer('marital_status_id')->nullable();
            $table->integer('nationality_id')->nullable();
            $table->string('national_id_card_number', 100)->nullable();
            $table->string('country_id', 50)->nullable();
            $table->string('state_id', 50)->nullable();
            $table->string('city_id', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile_num', 25)->nullable();
            $table->string('job_title', 100)->nullable();
            $table->integer('job_experience_id')->nullable();
            $table->integer('career_level_id')->nullable();
            $table->integer('industry_id')->nullable();
            $table->integer('functional_area_id')->nullable();
            $table->string('current_salary', 100)->nullable();
            $table->string('expected_salary', 100)->nullable();
            $table->string('salary_currency', 10)->nullable();
            $table->tinyText('street_address')->nullable();
            $table->integer('is_active')->nullable()->default(0);
            $table->boolean('verified')->default(false);
            $table->string('verification_token')->nullable();
            $table->string('provider', 35)->nullable();
            $table->string('provider_id')->nullable();
            $table->string('password', 100)->nullable();
            $table->rememberToken();
            $table->string('image', 100)->nullable();
            $table->string('cover_image', 100)->nullable();
            $table->string('lang', 10)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('is_immediate_available')->nullable()->default(true);
            $table->integer('num_profile_views')->nullable()->default(0);
            $table->integer('package_id')->nullable()->default(0);
            $table->string('payment_method', 50)->nullable()->comment('Payment method used (Stripe, PayPal, Razorpay, Paytm, PayU, Paystack, or Admin Assign)');
            $table->timestamp('package_start_date')->nullable();
            $table->timestamp('package_end_date')->nullable();
            $table->integer('jobs_quota')->nullable()->default(0);
            $table->integer('availed_jobs_quota')->nullable()->default(0);
            $table->text('search')->nullable()->fulltext('full_search');
            $table->boolean('is_subscribed')->nullable()->default(true);
            $table->text('video_link')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_code', 6)->nullable()->index('idx_email_verification_code');
            $table->timestamp('email_verification_code_expires_at')->nullable();
            $table->integer('email_verification_attempts')->nullable()->default(0);
            $table->boolean('is_email_verified')->nullable()->default(false)->index('idx_is_email_verified');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_package_end_at')->nullable();
            $table->timestamp('featured_package_start_at')->nullable();
            $table->string('api_token', 80)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
