<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaritalStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marital_statuses', function (Blueprint $table) {
            $table->id('id');
            $table->integer('marital_status_id')->nullable()->default('0');
            $table->string('marital_status', 40);
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
        Schema::dropIfExists('marital_statuses');
    }
}