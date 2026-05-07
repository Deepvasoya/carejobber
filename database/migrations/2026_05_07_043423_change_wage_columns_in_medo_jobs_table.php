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
        Schema::table('medo_jobs', function (Blueprint $table) {
            $table->decimal('wage_min', 9, 2)->nullable()->change();
            $table->decimal('wage_max', 9, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medo_jobs', function (Blueprint $table) {
            $table->decimal('wage_min', 6, 2)->nullable()->change();
            $table->decimal('wage_max', 6, 2)->nullable()->change();
        });
    }
};
