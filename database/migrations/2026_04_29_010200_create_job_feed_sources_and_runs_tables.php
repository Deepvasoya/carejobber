<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_feed_sources')) {
            Schema::create('job_feed_sources', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191);
                $table->string('slug', 191)->unique();
                $table->string('provider', 100)->default('custom');
                $table->string('source_url', 500)->nullable();
                $table->json('config')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_run_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('job_feed_runs')) {
            Schema::create('job_feed_runs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_feed_source_id')->constrained('job_feed_sources')->cascadeOnDelete();
                $table->string('status', 30)->default('running');
                $table->unsignedInteger('discovered_count')->default(0);
                $table->unsignedInteger('imported_count')->default(0);
                $table->unsignedInteger('updated_count')->default(0);
                $table->unsignedInteger('skipped_count')->default(0);
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_feed_runs');
        Schema::dropIfExists('job_feed_sources');
    }
};
