<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_claim_requests', function (Blueprint $table) {
            $table->string('claimant_name', 191)->nullable()->after('user_id');
            $table->string('claimant_email', 191)->nullable()->after('claimant_name');
            $table->string('claimant_job_title', 191)->nullable()->after('claimant_email');
        });
    }

    public function down(): void
    {
        Schema::table('company_claim_requests', function (Blueprint $table) {
            $table->dropColumn(['claimant_name', 'claimant_email', 'claimant_job_title']);
        });
    }
};
