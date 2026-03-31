<?php

namespace Database\Seeders;

use App\PackageCoupon;
use App\PackageCouponRedemption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * One-off sample coupons. Run:
 *   php artisan db:seed --class=Database\\Seeders\\PackageCouponSampleSeeder
 * Then remove this file from the repo if you do not want it in version control.
 */
class PackageCouponSampleSeeder extends Seeder
{
    private const CODES = ['SAMPLE_EMP10', 'SAMPLE_SEEK15', 'SAMPLE_RESUME5'];

    public function run(): void
    {
        if (!Schema::hasTable('package_coupons')) {
            $this->command?->warn('package_coupons table missing — run migrations first.');

            return;
        }

        $ids = PackageCoupon::whereIn('code', self::CODES)->pluck('id');
        if ($ids->isNotEmpty()) {
            PackageCouponRedemption::whereIn('package_coupon_id', $ids)->delete();
            PackageCoupon::whereIn('id', $ids)->delete();
        }

        $base = [
            'admin_note' => 'Sample seeder — change or delete in admin',
            'starts_at' => null,
            'ends_at' => now()->addYear(),
            'usage_limit_total' => null,
            'usage_limit_per_buyer' => null,
            'package_ids' => null,
            'allow_subscription_packages' => false,
            'is_active' => true,
        ];

        if (Schema::hasColumn('package_coupons', 'resume_promotion_package_ids')) {
            $base['resume_promotion_package_ids'] = null;
        }

        PackageCoupon::create(array_merge($base, [
            'code' => 'SAMPLE_EMP10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'max_discount_amount' => null,
            'min_package_price' => null,
            'package_for_scope' => 'employer',
        ]));

        PackageCoupon::create(array_merge($base, [
            'code' => 'SAMPLE_SEEK15',
            'discount_type' => 'percent',
            'discount_value' => 15,
            'max_discount_amount' => 25,
            'min_package_price' => null,
            'package_for_scope' => 'job_seeker',
        ]));

        $resumeRow = array_merge($base, [
            'code' => 'SAMPLE_RESUME5',
            'discount_type' => 'fixed',
            'discount_value' => 5,
            'max_discount_amount' => null,
            'min_package_price' => null,
            'package_for_scope' => 'resume_promotion',
        ]);

        PackageCoupon::create($resumeRow);

        $this->command?->info('Created 3 sample coupons: SAMPLE_EMP10, SAMPLE_SEEK15, SAMPLE_RESUME5');
    }
}
