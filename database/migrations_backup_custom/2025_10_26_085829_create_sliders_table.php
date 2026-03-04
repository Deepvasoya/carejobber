<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id('id');
            $table->integer('slider_id')->nullable()->default('0');
            $table->string('slider_image', 150)->nullable();
            $table->string('slider_heading', 250)->nullable();
            $table->string('slider_description')->nullable();
            $table->string('slider_link')->nullable();
            $table->string('slider_link_text', 100)->nullable();
            $table->string('lang', 10)->nullable()->default('en');
            $table->tinyInteger('is_default')->nullable()->default('0');
            $table->tinyInteger('is_active')->nullable()->default('1');
            $table->integer('sort_order')->nullable()->default('99999');
            $table->timestamps()->useCurrent();
            $table->string('updated_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sliders');
    }
}