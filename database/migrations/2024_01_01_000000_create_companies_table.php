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
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 155)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('ceo', 60)->nullable();
            $table->integer('industry_id')->nullable()->default(0);
            $table->integer('ownership_type_id')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->string('location', 155)->nullable();
            $table->integer('no_of_offices')->nullable();
            $table->string('website', 155)->nullable();
            $table->string('no_of_employees', 15)->nullable();
            $table->string('established_in', 12)->nullable();
            $table->string('fax', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->longText('logo')->nullable();
            $table->integer('country_id')->nullable()->default(0);
            $table->integer('state_id')->nullable()->default(0);
            $table->integer('city_id')->nullable()->default(0);
            $table->string('slug', 155)->nullable();
            $table->boolean('is_active')->nullable()->default(false);
            $table->boolean('is_featured')->nullable()->default(false);
            $table->boolean('verified')->nullable()->default(false);
            $table->string('verification_token')->nullable();
            $table->string('password', 100)->nullable();
            $table->string('api_token', 80)->nullable()->unique('api_token');
            $table->rememberToken();
            $table->text('map')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->string('facebook', 250)->nullable();
            $table->string('twitter', 250)->nullable();
            $table->string('linkedin', 250)->nullable();
            $table->string('google_plus', 250)->nullable();
            $table->string('pinterest', 250)->nullable();
            $table->integer('package_id')->nullable()->default(0);
            $table->timestamp('package_start_date')->nullable();
            $table->timestamp('package_end_date')->nullable();
            $table->integer('jobs_quota')->nullable()->default(0);
            $table->integer('availed_jobs_quota')->nullable()->default(0);
            $table->boolean('is_subscribed')->nullable()->default(true);
            $table->integer('cvs_package_id')->nullable();
            $table->timestamp('cvs_package_start_date')->nullable();
            $table->timestamp('cvs_package_end_date')->nullable();
            $table->integer('cvs_quota')->nullable();
            $table->integer('availed_cvs_quota')->nullable();
            $table->text('availed_cvs_ids')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('type')->nullable();
            $table->integer('count')->nullable();
            $table->longText('json_object')->nullable();
            $table->string('incorporation_or_formation_certificate')->nullable();
            $table->string('incorporation_or_formation_certificate_status')->nullable();
            $table->string('valid_tax_clearance')->nullable();
            $table->string('valid_tax_clearance_status')->nullable();
            $table->string('proof_of_address')->nullable();
            $table->string('proof_of_address_status')->nullable();
            $table->string('other_supporting_documents')->nullable();
            $table->string('other_supporting_documents_status')->nullable();
            $table->string('incorporation_or_formation_certificate_comment')->nullable();
            $table->string('valid_tax_clearance_comment')->nullable();
            $table->string('proof_of_address_comment')->nullable();
            $table->string('other_supporting_documents_comment')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('name_of_legal_representative')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
