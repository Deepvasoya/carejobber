<?php

use Illuminate\Database\Seeder;
use App\Models\Medo\Category;

class MedoCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'slug' => 'hca',
                'name' => 'Health Care Aide',
                'description' => 'Health Care Aides provide direct personal care to patients and residents under the supervision of nurses.',
            ],
            [
                'slug' => 'lpn',
                'name' => 'Licensed Practical Nurse',
                'description' => 'LPNs provide nursing care under the supervision of RNs and physicians, performing assessments, medication administration, and clinical procedures within their scope.',
            ],
            [
                'slug' => 'rn',
                'name' => 'Registered Nurse',
                'description' => 'RNs assess, plan, and deliver patient care across all settings, with full scope of nursing practice.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], [
                'name' => $category['name'],
                'description' => $category['description'],
            ]);
        }
    }
}
