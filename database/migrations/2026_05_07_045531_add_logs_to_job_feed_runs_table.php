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
        Schema::table('job_feed_runs', function (Blueprint $table) {
            $table->longText('imported_log')->nullable()->after('skipped_count');
            $table->longText('skipped_log')->nullable()->after('imported_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_feed_runs', function (Blueprint $table) {
            $table->dropColumn(['imported_log', 'skipped_log']);
        });
    }
};
