<?php

namespace Database\Seeders;

use App\Package;
use Illuminate\Database\Seeder;

class RecruiterPackagesSeeder extends Seeder
{
    /**
     * Seed employer packages (pay-per-post credits) and subscriptions for recruiter Stripe flow.
     */
    public function run(): void
    {
        $creditsPackages = [
            ['listings' => 3, 'price' => 273.00, 'rebate' => 30],
            ['listings' => 5, 'price' => 390.00, 'rebate' => 40],
            ['listings' => 10, 'price' => 650.00, 'rebate' => 50],
        ];

        foreach ($creditsPackages as $p) {
            Package::firstOrCreate(
                [
                    'package_for' => 'employer',
                    'type' => 'one_time_credits',
                    'package_num_listings' => $p['listings'],
                ],
                [
                    'package_title' => $p['listings'] . ' job postings',
                    'package_price' => $p['price'],
                    'package_num_days' => 0,
                    'country_code' => null,
                    'rebate_percent' => $p['rebate'],
                    'is_active' => true,
                ]
            );
        }

        $subscriptions = [
            ['months' => 3, 'days' => 90, 'price' => 1290.00],
            ['months' => 6, 'days' => 180, 'price' => 1990.00],
            ['months' => 12, 'days' => 365, 'price' => 2590.00],
        ];

        foreach ($subscriptions as $s) {
            Package::firstOrCreate(
                [
                    'package_for' => 'employer',
                    'type' => 'monthly_recurring',
                    'duration_days' => $s['days'],
                ],
                [
                    'package_title' => $s['months'] . ' months unlimited',
                    'package_price' => $s['price'],
                    'package_num_days' => $s['days'],
                    'package_num_listings' => 99999,
                    'country_code' => null,
                    'rebate_percent' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
