<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id('id');
            $table->integer('country_id')->nullable()->default('0');
            $table->string('country', 150)->nullable();
            $table->string('nationality', 150)->nullable();
            $table->tinyInteger('is_default')->nullable()->default('0');
            $table->tinyInteger('is_active')->nullable()->default('0');
            $table->integer('sort_order')->nullable()->default('9999');
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
        Schema::dropIfExists('countries');
    }
}