<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendToFriendMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_to_friend_messages', function (Blueprint $table) {
            $table->id('id');
            $table->string('your_name', 100)->nullable();
            $table->string('your_email', 100)->nullable();
            $table->mediumText('job_url')->nullable();
            $table->string('friend_name', 100)->nullable();
            $table->string('friend_email', 100)->nullable();
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
        Schema::dropIfExists('send_to_friend_messages');
    }
}