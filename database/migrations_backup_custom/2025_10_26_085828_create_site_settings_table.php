<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id('id');
            $table->string('site_name', 100)->nullable();
            $table->string('site_slogan', 150)->nullable();
            $table->string('site_logo', 100)->nullable();
            $table->string('site_phone_primary', 20)->nullable();
            $table->string('site_phone_secondary', 20)->nullable();
            $table->integer('default_country_id')->nullable();
            $table->string('default_currency_code', 4)->nullable();
            $table->string('site_street_address', 250)->nullable();
            $table->mediumText('site_google_map')->nullable();
            $table->string('mail_driver')->nullable()->default('smtp');
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
            $table->timestamps()->useCurrent();
            $table->string('updated_at')->nullable();
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
            $table->string('paypal_live_sandbox')->nullable()->default('sandbox');
            $table->string('stripe_key', 250)->nullable();
            $table->string('stripe_secret', 250)->nullable();
            $table->mediumText('bank_details')->nullable();
            $table->integer('listing_age')->default('15');
            $table->tinyInteger('country_specific_site')->nullable()->default('0');
            $table->tinyInteger('is_paypal_active')->nullable()->default('1');
            $table->tinyInteger('is_bank_transfer_active')->nullable()->default('1');
            $table->tinyInteger('is_jobseeker_package_active')->default('0');
            $table->tinyInteger('is_stripe_active')->nullable()->default('1');
            $table->tinyInteger('is_slider_active')->nullable()->default('0');
            $table->string('mailchimp_api_key')->nullable();
            $table->string('mailchimp_list_name')->nullable();
            $table->string('mailchimp_list_id')->nullable();
            $table->tinyInteger('is_company_package_active')->default('1');
            $table->tinyInteger('is_payu_active')->nullable()->default('1');
            $table->string('payu_money_mode', 255)->nullable();
            $table->string('payu_money_key', 255)->nullable();
            $table->string('salt', 255)->nullable();
            $table->timestamp('check_time')->nullable();
            $table->mediumText('ganalytics')->nullable();
            $table->text('google_tag_manager_for_body')->nullable();
            $table->text('google_tag_manager_for_head')->nullable();
            $table->string('username_jobg8', 255)->nullable();
            $table->string('password_jobg8', 255)->nullable();
            $table->string('accountnumber_jobg8', 255)->nullable();
            $table->tinyInteger('auto_approval_company')->default('0');
            $table->tinyInteger('auto_approval_job')->default('0');
            $table->string('razorpay_key', 255)->nullable();
            $table->string('razorpay_secret', 255)->nullable();
            $table->tinyInteger('is_razorpay_active')->nullable()->default('0');
            $table->string('paytm_merchant_key', 255)->nullable();
            $table->string('paytm_merchant_id', 255)->nullable();
            $table->string('paytm_website', 255)->nullable();
            $table->string('paytm_industry_type', 255)->nullable();
            $table->string('paytm_channel_id', 255)->nullable();
            $table->tinyInteger('is_paytm_active')->nullable()->default('0');
            $table->string('paystack_key', 255)->nullable();
            $table->string('paystack_secret', 255)->nullable();
            $table->tinyInteger('is_paystack_active')->nullable()->default('0');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_settings');
    }
}