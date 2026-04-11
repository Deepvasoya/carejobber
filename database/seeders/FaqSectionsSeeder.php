<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\FaqSection;
use Illuminate\Support\Str;

class FaqSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'name' => 'JOBSEEKER',
                'slug' => 'jobseeker',
                'description' => 'Frequently asked questions for job seekers',
                'sort_order' => 1,
                'lang' => 'en',
                'is_active' => 1,
            ],
            [
                'name' => 'EMPLOYERS',
                'slug' => 'employers',
                'description' => 'Frequently asked questions for employers',
                'sort_order' => 2,
                'lang' => 'en',
                'is_active' => 1,
            ],
            [
                'name' => 'TRAINING',
                'slug' => 'training',
                'description' => 'Frequently asked questions about training',
                'sort_order' => 3,
                'lang' => 'en',
                'is_active' => 1,
            ],
        ];

        foreach ($sections as $section) {
            FaqSection::updateOrCreate(
                ['slug' => $section['slug'], 'lang' => $section['lang']],
                $section
            );
        }
    }
}
