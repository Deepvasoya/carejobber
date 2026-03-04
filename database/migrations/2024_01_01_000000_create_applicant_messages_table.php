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
        Schema::create('applicant_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('user_name', 150)->nullable();
            $table->integer('from_id')->nullable();
            $table->integer('to_id')->nullable();
            $table->string('to_email', 100)->nullable();
            $table->string('to_name', 100)->nullable();
            $table->string('from_name', 100)->nullable();
            $table->string('from_email', 100)->nullable();
            $table->string('from_phone', 20)->nullable();
            $table->mediumText('message_txt')->nullable();
            $table->string('subject', 200)->nullable();
            $table->boolean('is_read')->nullable()->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_messages');
    }
};
