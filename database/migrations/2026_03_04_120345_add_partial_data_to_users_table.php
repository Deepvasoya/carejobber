<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add partial_data JSON for partial resume view (work history + education).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('partial_data')->nullable()->after('is_active')->comment('Partial resume: work history, education for employer preview');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('partial_data');
        });
    }
};
