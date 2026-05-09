<?php

namespace Database\Seeders;

use App\Models\Medo\Category;
use App\Models\Medo\CategoryProvinceSetting;
use App\Models\Medo\Province;
use App\Models\Medo\SalaryGrid;
use App\Models\Medo\Union;
use Illuminate\Database\Seeder;

class MedoSalaryGridSeeder extends Seeder
{
    public function run(): void
    {
        $ab = Province::where('slug', 'ab')->first();

        if (! $ab) {
            return;
        }

        $sources = [
            'aupe' => 'https://www.aupe.org/sites/default/files/2026-01/2028-03-31%20-%20AHS%20NC%20-%20Draft%20Agreement%20-%20AUPE%20Website_1.pdf',
            'una' => 'https://www.una.ca/files/uploads/2026/4/Provincial_Collective_Agreement_FINAL_-_UNA_Signed_%282026-04-16.pdf',
            // HSAA is kept here as the official source for the next health-science category expansion.
            // The current launch categories are HCA, LPN, and RN, which map to AUPE/UNA grids.
            'hsaa' => 'https://hsaa.ca/post/ahs-collective-agreement-implementation-faq',
        ];

        $grids = [
            // AHS/AUPE Nursing Care, effective April 1, 2026.
            // Certified HCA uses the post-2024 compressed five-step scale.
            'hca' => [
                'union' => 'aupe',
                'effective_date' => '2026-04-01',
                'rates' => [26.42, 27.29, 27.90, 28.71, 29.58],
            ],
            // AHS/AUPE Nursing Care, effective April 1, 2026.
            'lpn' => [
                'union' => 'aupe',
                'effective_date' => '2026-04-01',
                'rates' => [33.52, 34.94, 36.34, 37.74, 39.20, 40.53, 42.18, 43.85],
            ],
            // UNA Provincial Collective Agreement, Registered Nurse/RPN grid, effective April 1, 2026.
            'rn' => [
                'union' => 'una',
                'effective_date' => '2026-04-01',
                'rates' => [42.84, 44.56, 46.34, 48.19, 50.12, 52.13, 54.21, 56.38, 58.64],
            ],
        ];

        foreach ($grids as $categorySlug => $grid) {
            $category = Category::where('slug', $categorySlug)->first();
            $union = Union::where('slug', $grid['union'])->first();

            if (! $category || ! $union) {
                continue;
            }

            foreach ($grid['rates'] as $index => $rate) {
                SalaryGrid::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'province_id' => $ab->id,
                        'union_id' => $union->id,
                        'step' => $index + 1,
                    ],
                    [
                        'hourly_rate' => $rate,
                        'effective_date' => $grid['effective_date'],
                        'source_url' => $sources[$grid['union']],
                    ]
                );
            }

            CategoryProvinceSetting::where('category_id', $category->id)
                ->where('province_id', $ab->id)
                ->update([
                    'union_id' => $union->id,
                    'wage_min' => min($grid['rates']),
                    'wage_max' => max($grid['rates']),
                ]);
        }
    }
}
