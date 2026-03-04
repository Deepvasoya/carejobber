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
        Schema::table('job_apply', function (Blueprint $table) {
            $table->text('cover_letter')->nullable()->after('salary_currency');
            $table->string('resume_source', 50)->nullable()->after('cv_id')->comment('existing_cv or uploaded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_apply', function (Blueprint $table) {
            $table->dropColumn(['cover_letter', 'resume_source']);
        });
    }
};
