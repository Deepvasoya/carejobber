<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRazorpayPaytmFieldsToSiteSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Razorpay fields
            $table->string('razorpay_key')->nullable();
            $table->string('razorpay_secret')->nullable();
            $table->boolean('is_razorpay_active')->default(false);
            
            // Paytm fields
            $table->string('paytm_merchant_key')->nullable();
            $table->string('paytm_merchant_id')->nullable();
            $table->string('paytm_website')->nullable();
            $table->string('paytm_industry_type')->nullable();
            $table->string('paytm_channel_id')->nullable();
            $table->boolean('is_paytm_active')->default(false);

            // Paystack fields
            $table->string('paystack_key')->nullable();
            $table->string('paystack_secret')->nullable();
            $table->boolean('is_paystack_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_key',
                'razorpay_secret',
                'is_razorpay_active',
                'paytm_merchant_key',
                'paytm_merchant_id',
                'paytm_website',
                'paytm_industry_type',
                'paytm_channel_id',
                'is_paytm_active',
                'paystack_key',
                'paystack_secret',
                'is_paystack_active'
            ]);
        });
    }
} 