<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayuTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payu_transactions', function (Blueprint $table) {
            $table->id('id');
            $table->integer('paid_for_id')->nullable();
            $table->string('paid_for_type', 255)->nullable();
            $table->string('transaction_id', 255);
            $table->text('gateway');
            $table->text('body');
            $table->string('destination', 255);
            $table->text('hash');
            $table->text('response')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('payu_transactions');
    }
}