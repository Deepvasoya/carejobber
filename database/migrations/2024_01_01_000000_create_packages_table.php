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
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package_title', 150)->nullable();
            $table->float('package_price', 7)->nullable()->default(0);
            $table->integer('package_num_days')->nullable()->default(0);
            $table->integer('package_num_listings')->nullable()->default(0);
            $table->enum('package_for', ['job_seeker', 'employer', 'cv_search', 'make_featured'])->nullable()->default('job_seeker');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
