<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJobCategories extends Command
{
    protected $signature = 'sync:job-categories';
    protected $description = 'Sync job categories from functional areas';

    public function handle()
    {
        $this->info('Syncing job categories from functional areas...');
        
        $functionalAreas = DB::table('functional_areas')->get();
        $added = 0;
        
        foreach ($functionalAreas as $fa) {
            $exists = DB::table('job_categories')
                ->where('job_category_id', $fa->functional_area_id)
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
                    'slug' => $fa->slug,
                    'created_at' => $fa->created_at,
                    'updated_at' => $fa->updated_at,
                ]);
                $this->info("Added: {$fa->functional_area} (ID: {$fa->functional_area_id})");
                $added++;
            }
        }
        
        $this->info("Sync completed. Added {$added} job categories.");
        
        // Also sync job_category_id in jobs table
        $updated = DB::statement('UPDATE jobs SET job_category_id = functional_area_id WHERE functional_area_id IS NOT NULL AND job_category_id IS NULL');
        $this->info("Updated jobs table with job_category_id.");
        
        return 0;
    }
}
