<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_messages', function (Blueprint $table) {
            $table->id('id');
            $table->integer('company_id')->nullable();
            $table->integer('seeker_id')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('unviewed');
            $table->string('type')->nullable()->default('message');
            $table->timestamps()->nullable();
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
        Schema::dropIfExists('company_messages');
    }
}