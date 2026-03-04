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
        Schema::create('degree_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('degree_level_id')->nullable()->default(0);
            $table->integer('degree_type_id')->nullable()->default(0);
            $table->string('degree_type', 200)->nullable();
            $table->boolean('is_default')->nullable()->default(false);
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
        Schema::dropIfExists('degree_types');
    }
};
