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
        Schema::create('countries_details', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('country_id')->nullable()->default(0);
            $table->string('sort_name', 5);
            $table->integer('phone_code');
            $table->string('currency', 60)->nullable();
            $table->string('code', 7)->nullable();
            $table->string('symbol', 7)->nullable();
            $table->string('thousand_separator', 2)->nullable();
            $table->string('decimal_separator', 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries_details');
    }
};
