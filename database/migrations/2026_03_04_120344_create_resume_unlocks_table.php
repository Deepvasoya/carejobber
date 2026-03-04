<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Per-resume Stripe unlock: one-time payment to unlock full resume details.
     */
    public function up(): void
    {
        Schema::create('resume_unlocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('user_id')->index()->comment('Job seeker user_id');
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('CAD');
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('payment_method', 50)->default('stripe')->comment('stripe, credits, admin');
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();
            
            $table->unique(['company_id', 'user_id'], 'company_user_unlock_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_unlocks');
    }
};
