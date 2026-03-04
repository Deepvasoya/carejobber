<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportAbuseMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_abuse_messages', function (Blueprint $table) {
            $table->id('id');
            $table->string('your_name', 100)->nullable();
            $table->string('your_email', 100)->nullable();
            $table->string('job_url')->nullable();
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
        Schema::dropIfExists('report_abuse_messages');
    }
}