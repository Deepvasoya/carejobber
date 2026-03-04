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
        Schema::create('jobsb', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
            $table->text('benefits')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->boolean('is_freelance')->nullable()->default(false);
            $table->integer('career_level_id')->nullable();
            $table->integer('salary_from')->nullable();
            $table->integer('salary_to')->nullable();
            $table->boolean('hide_salary')->nullable()->default(false);
            $table->string('salary_currency', 5)->nullable();
            $table->integer('salary_period_id')->nullable();
            $table->integer('functional_area_id')->nullable();
            $table->integer('job_type_id')->nullable();
            $table->integer('job_shift_id')->nullable();
            $table->integer('num_of_positions')->nullable();
            $table->integer('gender_id')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->integer('degree_level_id')->nullable();
            $table->integer('job_experience_id')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_featured')->nullable()->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->text('search')->nullable()->fulltext('full_search');
            $table->string('slug', 210)->nullable();
            $table->string('reference')->nullable();
            $table->string('location')->nullable();
            $table->string('logo')->nullable();
            $table->string('type')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('job_advertiser')->nullable();
            $table->string('application_url')->nullable();
            $table->longText('json_object')->nullable();
            $table->enum('external_job', ['yes', 'no'])->default('no');
            $table->string('job_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobsb');
    }
};
