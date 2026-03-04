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
        Schema::create('report_abuse_company_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('your_name', 100)->nullable();
            $table->string('your_email', 100)->nullable();
            $table->mediumText('company_url')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_abuse_company_messages');
    }
};
