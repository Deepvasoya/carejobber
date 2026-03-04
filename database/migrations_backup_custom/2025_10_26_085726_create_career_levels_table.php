<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCareerLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('career_levels', function (Blueprint $table) {
            $table->id('id');
            $table->integer('career_level_id')->nullable()->default('0');
            $table->string('career_level', 200)->nullable();
            $table->tinyInteger('is_default')->nullable()->default('0');
            $table->tinyInteger('is_active')->nullable();
            $table->integer('sort_order')->nullable()->default('99999');
            $table->string('lang', 10)->nullable()->default('en');
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
        Schema::dropIfExists('career_levels');
    }
}