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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_name', 100)->nullable();
            $table->string('site_slogan', 150)->nullable();
            $table->string('site_logo', 100)->nullable();
            $table->string('site_phone_primary', 20)->nullable();
            $table->string('site_phone_secondary', 20)->nullable();
            $table->integer('default_country_id')->nullable();
            $table->string('default_currency_code', 4)->nullable();
            $table->string('site_street_address', 250)->nullable();
            $table->mediumText('site_google_map')->nullable();
            $table->enum('mail_driver', ['array', 'log', 'sparkpost', 'ses', 'mandrill', 'mailgun', 'sendmail', 'smtp', 'mail'])->nullable()->default('smtp');
            $table->string('mail_host', 100)->nullable();
            $table->integer('mail_port')->nullable();
            $table->string('mail_from_address', 100)->nullable();
            $table->string('mail_from_name', 100)->nullable();
            $table->string('mail_to_address', 100)->nullable();
            $table->string('mail_to_name', 100)->nullable();
            $table->string('mail_encryption', 10)->nullable();
            $table->string('mail_username', 100)->nullable();
            $table->string('mail_password', 100)->nullable();
            $table->string('mail_sendmail', 50)->nullable();
            $table->string('mail_pretend', 50)->nullable();
            $table->string('mailgun_domain', 100)->nullable();
            $table->string('mailgun_secret', 100)->nullable();
            $table->string('mandrill_secret', 100)->nullable();
            $table->string('sparkpost_secret', 100)->nullable();
            $table->string('ses_key', 100)->nullable();
            $table->string('ses_secret', 100)->nullable();
            $table->string('ses_region', 100)->nullable();
            $table->text('facebook_address')->nullable();
            $table->text('twitter_address')->nullable();
            $table->text('google_plus_address')->nullable();
            $table->text('youtube_address')->nullable();
            $table->text('instagram_address')->nullable();
            $table->text('pinterest_address')->nullable();
            $table->text('linkedin_address')->nullable();
            $table->text('tumblr_address')->nullable();
            $table->text('flickr_address')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->mediumText('index_page_below_top_employes_ad')->nullable();
            $table->mediumText('above_footer_ad')->nullable();
            $table->mediumText('dashboard_page_ad')->nullable();
            $table->mediumText('cms_page_ad')->nullable();
            $table->mediumText('listing_page_vertical_ad')->nullable();
            $table->mediumText('listing_page_horizontal_ad')->nullable();
            $table->string('nocaptcha_sitekey', 150)->nullable();
            $table->string('nocaptcha_secret', 150)->nullable();
            $table->string('facebook_app_id', 150)->nullable();
            $table->string('facebeek_app_secret', 150)->nullable();
            $table->string('google_app_id', 150)->nullable();
            $table->string('google_app_secret', 150)->nullable();
            $table->string('twitter_app_id', 150)->nullable();
            $table->string('twitter_app_secret', 150)->nullable();
            $table->string('paypal_account', 250)->nullable();
            $table->string('paypal_client_id', 250)->nullable();
            $table->string('paypal_secret', 250)->nullable();
            $table->enum('paypal_live_sandbox', ['live', 'sandbox'])->nullable()->default('sandbox');
            $table->string('stripe_key', 250)->nullable();
            $table->string('stripe_secret', 250)->nullable();
            $table->mediumText('bank_details')->nullable();
            $table->integer('listing_age')->default(15);
            $table->boolean('country_specific_site')->nullable()->default(false);
            $table->boolean('is_paypal_active')->nullable()->default(true);
            $table->boolean('is_bank_transfer_active')->nullable()->default(true);
            $table->boolean('is_jobseeker_package_active')->default(false);
            $table->boolean('is_stripe_active')->nullable()->default(true);
            $table->boolean('is_slider_active')->nullable()->default(false);
            $table->tinyText('mailchimp_api_key')->nullable();
            $table->tinyText('mailchimp_list_name')->nullable();
            $table->tinyText('mailchimp_list_id')->nullable();
            $table->boolean('is_company_package_active')->default(true);
            $table->boolean('is_payu_active')->nullable()->default(true);
            $table->string('payu_money_mode')->nullable();
            $table->string('payu_money_key')->nullable();
            $table->string('salt')->nullable();
            $table->timestamp('check_time')->nullable();
            $table->mediumText('ganalytics')->nullable();
            $table->text('google_tag_manager_for_body')->nullable();
            $table->text('google_tag_manager_for_head')->nullable();
            $table->string('username_jobg8')->nullable();
            $table->string('password_jobg8')->nullable();
            $table->string('accountnumber_jobg8')->nullable();
            $table->boolean('auto_approval_company')->default(false);
            $table->boolean('auto_approval_job')->default(false);
            $table->string('razorpay_key')->nullable();
            $table->string('razorpay_secret')->nullable();
            $table->boolean('is_razorpay_active')->nullable()->default(false);
            $table->string('paytm_merchant_key')->nullable();
            $table->string('paytm_merchant_id')->nullable();
            $table->string('paytm_website')->nullable();
            $table->string('paytm_industry_type')->nullable();
            $table->string('paytm_channel_id')->nullable();
            $table->boolean('is_paytm_active')->nullable()->default(false);
            $table->string('paystack_key')->nullable();
            $table->string('paystack_secret')->nullable();
            $table->boolean('is_paystack_active')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
