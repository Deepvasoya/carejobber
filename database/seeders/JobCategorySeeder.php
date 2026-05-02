<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\JobCategory;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobCategories = [
            'Information Technology',
            'Healthcare & Medical',
            'Finance & Banking',
            'Education & Training',
            'Marketing & Sales',
            'Human Resources',
            'Engineering',
            'Customer Service',
            'Administration',
            'Manufacturing',
            'Retail & E-commerce',
            'Hospitality & Tourism',
            'Legal Services',
            'Media & Communications',
            'Construction',
            'Transportation & Logistics',
            'Real Estate',
            'Non-Profit & Social Services',
            'Government & Public Sector',
            'Arts & Creative'
        ];

        foreach ($jobCategories as $index => $categoryName) {
            $jobCategory = new JobCategory();
            $jobCategory->job_category_id = $index + 1;
            $jobCategory->job_category = $categoryName;
            $jobCategory->is_default = true;
            $jobCategory->is_active = true;
            $jobCategory->sort_order = $index + 1;
            $jobCategory->lang = 'en';
            $jobCategory->save();
        }
    }
}