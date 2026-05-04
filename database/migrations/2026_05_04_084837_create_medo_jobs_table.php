<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedoJobsTable extends Migration
{
    public function up()
    {
        Schema::create('medo_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('source');
            $table->string('slug');
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained('medo_categories')->onDelete('cascade');
            $table->foreignId('province_id')->constrained('medo_provinces')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('medo_cities')->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('medo_employers')->onDelete('cascade');
            $table->string('facility_name')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'casual'])->nullable();
            $table->enum('shift_type', ['days', 'evenings', 'nights', 'rotating', 'weekends'])->nullable();
            $table->enum('setting', ['acute', 'ltc', 'community', 'clinic'])->nullable();
            $table->decimal('wage_min', 6, 2)->nullable();
            $table->decimal('wage_max', 6, 2)->nullable();
            $table->enum('wage_period', ['hourly', 'annual'])->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('apply_url')->nullable();
            $table->boolean('is_new_grad_friendly')->default(false);
            $table->boolean('has_signing_bonus')->default(false);
            $table->timestamps();

            $table->index(['category_id', 'city_id', 'expires_at']);
            $table->index(['category_id', 'province_id', 'expires_at']);
            $table->index(['posted_at']);
            $table->unique(['external_id', 'source']);
            $table->unique(['slug', 'city_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medo_jobs');
    }
}
