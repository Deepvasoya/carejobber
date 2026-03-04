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
        Schema::table('jobs', function (Blueprint $table) {
            $table->integer('display_duration_days')->default(30)->after('expiry_date')->comment('How long job is displayed: 30, 60, 90, 180 days');
            $table->timestamp('display_end_date')->nullable()->after('display_duration_days')->comment('Auto-calculated: created_at + display_duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['display_duration_days', 'display_end_date']);
        });
    }
};
