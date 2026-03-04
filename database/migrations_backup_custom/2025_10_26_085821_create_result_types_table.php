<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_types', function (Blueprint $table) {
            $table->id('id');
            $table->integer('result_type_id')->nullable()->default('0');
            $table->string('result_type', 40);
            $table->integer('is_default')->nullable()->default('0');
            $table->integer('is_active')->default('1');
            $table->integer('sort_order')->default('9999');
            $table->string('lang', 10)->default('en');
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
        Schema::dropIfExists('result_types');
    }
}