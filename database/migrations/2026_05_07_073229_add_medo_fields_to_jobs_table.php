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
            $table->unsignedBigInteger('medo_category_id')->nullable();
            $table->unsignedBigInteger('medo_province_id')->nullable();
            $table->unsignedBigInteger('medo_city_id')->nullable();
            $table->unsignedBigInteger('medo_employer_id')->nullable();
            $table->decimal('wage_min', 8, 2)->nullable();
            $table->decimal('wage_max', 8, 2)->nullable();
            $table->string('wage_period')->nullable();
            $table->string('external_id')->nullable();
            $table->string('source')->nullable();
            $table->string('apply_url', 500)->nullable();
            
            $table->index('medo_category_id');
            $table->index('medo_city_id');
            $table->index('external_id');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'medo_category_id',
                'medo_province_id',
                'medo_city_id',
                'medo_employer_id',
                'wage_min',
                'wage_max',
                'wage_period',
                'external_id',
                'source',
                'apply_url',
            ]);
        });
    }
};
