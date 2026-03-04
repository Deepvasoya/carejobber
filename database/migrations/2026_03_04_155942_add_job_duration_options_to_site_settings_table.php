<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('job_duration_options')->nullable()->after('location_levels')->comment('Available duration options: [30, 60, 90, 180, 365]');
            $table->integer('default_job_duration')->default(30)->after('job_duration_options')->comment('Default job display duration in days');
        });
        
        // Set default values
        DB::table('site_settings')->update([
            'job_duration_options' => json_encode([30, 60, 90, 120, 180, 365]),
            'default_job_duration' => 30,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['job_duration_options', 'default_job_duration']);
        });
    }
};
