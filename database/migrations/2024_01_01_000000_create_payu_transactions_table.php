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
        Schema::create('payu_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('paid_for_id')->nullable();
            $table->string('paid_for_type')->nullable();
            $table->string('transaction_id');
            $table->text('gateway');
            $table->text('body');
            $table->string('destination');
            $table->text('hash');
            $table->text('response')->nullable();
            $table->enum('status', ['pending', 'failed', 'successful', 'invalid'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payu_transactions');
    }
};
