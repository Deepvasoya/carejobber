<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_promotion_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('duration_days');
            $table->decimal('price', 10, 2);
            $table->string('currency', 5)->default('CAD');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        // Insert default packages
        DB::table('resume_promotion_packages')->insert([
            [
                'name' => '90 Days Promotion',
                'duration_days' => 90,
                'price' => 15.00,
                'currency' => 'CAD',
                'description' => 'Promote your resume for 90 days',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '6 Months Promotion',
                'duration_days' => 180,
                'price' => 25.00,
                'currency' => 'CAD',
                'description' => 'Promote your resume for 6 months',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '1 Year Promotion',
                'duration_days' => 365,
                'price' => 40.00,
                'currency' => 'CAD',
                'description' => 'Promote your resume for 1 year',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Add promotion fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_resume_promoted')->default(0)->after('is_featured');
            $table->timestamp('promotion_start_date')->nullable()->after('is_resume_promoted');
            $table->timestamp('promotion_end_date')->nullable()->after('promotion_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_resume_promoted', 'promotion_start_date', 'promotion_end_date']);
        });
        
        Schema::dropIfExists('resume_promotion_packages');
    }
};
