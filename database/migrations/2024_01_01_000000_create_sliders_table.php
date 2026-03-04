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
        Schema::create('sliders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('slider_id')->nullable()->default(0);
            $table->string('slider_image', 150)->nullable();
            $table->string('slider_heading', 250)->nullable();
            $table->tinyText('slider_description')->nullable();
            $table->tinyText('slider_link')->nullable();
            $table->string('slider_link_text', 100)->nullable();
            $table->string('lang', 10)->nullable()->default('en');
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(true);
            $table->integer('sort_order')->nullable()->default(99999);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
