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
        Schema::table('users', function (Blueprint $table) {
            $table->json('custom_field_data')->nullable()->after('partial_data');
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->json('custom_field_data')->nullable();
        });
        Schema::table('profile_cvs', function (Blueprint $table) {
            $table->json('custom_field_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('custom_field_data');
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('custom_field_data');
        });
        Schema::table('profile_cvs', function (Blueprint $table) {
            $table->dropColumn('custom_field_data');
        });
    }
};
