<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration consolidates functional_area into job_category
     * by copying data and updating references.
     */
    public function up(): void
    {
        // Step 1: Copy functional_areas data to job_categories if not already present
        $functionalAreas = DB::table('functional_areas')->get();
        
        foreach ($functionalAreas as $fa) {
            // Check if this functional area already exists in job_categories
            $exists = DB::table('job_categories')
                ->where('job_category', $fa->functional_area)
                ->where('lang', $fa->lang)
                ->exists();
            
            if (!$exists) {
                DB::table('job_categories')->insert([
                    'job_category_id' => $fa->functional_area_id,
                    'job_category' => $fa->functional_area,
                    'is_default' => $fa->is_default,
                    'image' => $fa->image,
                    'is_active' => $fa->is_active,
                    'sort_order' => $fa->sort_order,
                    'lang' => $fa->lang,
                    'slug' => $fa->slug ?? null,
                    'created_at' => $fa->created_at,
                    'updated_at' => $fa->updated_at,
                ]);
            }
        }
        
        // Step 2: Add job_category_id column to jobs table if it doesn't exist
        if (!Schema::hasColumn('jobs', 'job_category_id')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->integer('job_category_id')->nullable()->after('functional_area_id');
            });
        }
        
        // Step 3: Copy functional_area_id to job_category_id in jobs table
        DB::statement('UPDATE jobs SET job_category_id = functional_area_id WHERE functional_area_id IS NOT NULL');
        
        // Step 4: Copy functional_area_id to job_category_id in users table (already has column from previous migration)
        DB::statement('UPDATE users SET job_category_id = functional_area_id WHERE functional_area_id IS NOT NULL');
        
        // Step 5: Drop functional_area_id columns (commented out for safety - uncomment after verification)
        // Schema::table('jobs', function (Blueprint $table) {
        //     $table->dropColumn('functional_area_id');
        // });
        // 
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn('functional_area_id');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore functional_area_id columns if they were dropped
        if (!Schema::hasColumn('jobs', 'functional_area_id')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->integer('functional_area_id')->nullable();
            });
        }
        
        if (!Schema::hasColumn('users', 'functional_area_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('functional_area_id')->nullable();
            });
        }
        
        // Copy data back
        DB::statement('UPDATE jobs SET functional_area_id = job_category_id WHERE job_category_id IS NOT NULL');
        DB::statement('UPDATE users SET functional_area_id = job_category_id WHERE job_category_id IS NOT NULL');
        
        // Drop job_category_id from jobs
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('job_category_id');
        });
    }
};
