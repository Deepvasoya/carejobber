<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPushTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_push_tokens', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('user_id');
            $table->string('push_token', 255);
            $table->string('platform');
            $table->string('device_id', 255)->nullable();
            $table->timestamps()->nullable();
            $table->string('updated_at')->nullable();

            $table->index(['user_id', 'device_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_push_tokens');
    }
}