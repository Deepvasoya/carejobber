<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id('id');
            $table->string('package_title', 150)->nullable();
            $table->float('package_price')->nullable()->default('0.00');
            $table->integer('package_num_days')->nullable()->default('0');
            $table->integer('package_num_listings')->nullable()->default('0');
            $table->string('package_for')->nullable()->default('job_seeker');
            $table->timestamps()->nullable()->useCurrent();
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
        Schema::dropIfExists('packages');
    }
}