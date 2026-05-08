<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medo_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('employer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medo_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('employer_id')->nullable(false)->change();
        });
    }
};
