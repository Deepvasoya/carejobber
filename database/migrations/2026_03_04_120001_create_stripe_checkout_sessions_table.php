<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Store Stripe Checkout session metadata to fulfill on success/webhook.
     */
    public function up(): void
    {
        Schema::create('stripe_checkout_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('session_id', 255)->unique();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('package_id');
            $table->string('country_code', 2)->nullable();
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_checkout_sessions');
    }
};
