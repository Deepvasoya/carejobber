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
        Schema::create('functional_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('functional_area_id')->nullable()->default(0);
            $table->string('functional_area', 200)->nullable();
            $table->boolean('is_default')->nullable()->default(true);
            $table->string('image')->nullable();
            $table->boolean('is_active')->nullable();
            $table->integer('sort_order')->nullable()->default(99999);
            $table->string('lang', 10)->nullable()->default('en');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('functional_areas');
    }
};
