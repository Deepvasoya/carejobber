<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'View Admin Users', 'slug' => 'admin_users.view', 'module' => 'Admin Users'],
            ['name' => 'Create Admin Users', 'slug' => 'admin_users.create', 'module' => 'Admin Users'],
            ['name' => 'Edit Admin Users', 'slug' => 'admin_users.edit', 'module' => 'Admin Users'],
            ['name' => 'Delete Admin Users', 'slug' => 'admin_users.delete', 'module' => 'Admin Users'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'Admin Users'],
            ['name' => 'List Jobs', 'slug' => 'jobs.view', 'module' => 'Jobs'],
            ['name' => 'Create Jobs', 'slug' => 'jobs.create', 'module' => 'Jobs'],
            ['name' => 'Edit Jobs', 'slug' => 'jobs.edit', 'module' => 'Jobs'],
            ['name' => 'Delete Jobs', 'slug' => 'jobs.delete', 'module' => 'Jobs'],
            ['name' => 'Import Jobs', 'slug' => 'jobs.import', 'module' => 'Jobs'],
            ['name' => 'List Companies', 'slug' => 'companies.view', 'module' => 'Companies'],
            ['name' => 'Create Companies', 'slug' => 'companies.create', 'module' => 'Companies'],
            ['name' => 'Edit Companies', 'slug' => 'companies.edit', 'module' => 'Companies'],
            ['name' => 'Delete Companies', 'slug' => 'companies.delete', 'module' => 'Companies'],
            ['name' => 'List Site Users', 'slug' => 'site_users.view', 'module' => 'User Profiles'],
            ['name' => 'Create Site Users', 'slug' => 'site_users.create', 'module' => 'User Profiles'],
            ['name' => 'Edit Site Users', 'slug' => 'site_users.edit', 'module' => 'User Profiles'],
            ['name' => 'Delete Site Users', 'slug' => 'site_users.delete', 'module' => 'User Profiles'],
            ['name' => 'List CMS Pages', 'slug' => 'cms.view', 'module' => 'C.M.S'],
            ['name' => 'Create CMS Pages', 'slug' => 'cms.create', 'module' => 'C.M.S'],
            ['name' => 'Edit CMS Pages', 'slug' => 'cms.edit', 'module' => 'C.M.S'],
            ['name' => 'Delete CMS Pages', 'slug' => 'cms.delete', 'module' => 'C.M.S'],
            ['name' => 'Manage Blog', 'slug' => 'blog.manage', 'module' => 'Blog'],
            ['name' => 'Manage SEO', 'slug' => 'seo.manage', 'module' => 'SEO'],
            ['name' => 'List FAQs', 'slug' => 'faq.view', 'module' => 'FAQs'],
            ['name' => 'Create/Edit FAQs', 'slug' => 'faq.manage', 'module' => 'FAQs'],
            ['name' => 'List Videos', 'slug' => 'videos.view', 'module' => 'Videos'],
            ['name' => 'Create/Edit Videos', 'slug' => 'videos.manage', 'module' => 'Videos'],
            ['name' => 'List Testimonials', 'slug' => 'testimonials.view', 'module' => 'Testimonials'],
            ['name' => 'Create/Edit Testimonials', 'slug' => 'testimonials.manage', 'module' => 'Testimonials'],
            ['name' => 'List Sliders', 'slug' => 'sliders.view', 'module' => 'Slider'],
            ['name' => 'Create/Edit Sliders', 'slug' => 'sliders.manage', 'module' => 'Slider'],
            ['name' => 'Manage Languages', 'slug' => 'languages.manage', 'module' => 'Translation'],
            ['name' => 'Manage Countries', 'slug' => 'countries.manage', 'module' => 'Location'],
            ['name' => 'Manage Country Details', 'slug' => 'country_details.manage', 'module' => 'Location'],
            ['name' => 'Manage States', 'slug' => 'states.manage', 'module' => 'Location'],
            ['name' => 'Manage Cities', 'slug' => 'cities.manage', 'module' => 'Location'],
            ['name' => 'Manage Packages', 'slug' => 'packages.manage', 'module' => 'Packages'],
            ['name' => 'View Payment History', 'slug' => 'payments.view', 'module' => 'Payments'],
            ['name' => 'View Referrals', 'slug' => 'referrals.view', 'module' => 'Referral'],
            ['name' => 'Manage Job Attributes', 'slug' => 'job_attributes.manage', 'module' => 'Job Attributes'],
            ['name' => 'Site Settings', 'slug' => 'site_settings.manage', 'module' => 'Manage'],
            ['name' => 'Manage Widgets', 'slug' => 'widgets.manage', 'module' => 'Manage'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->insertOrIgnore(array_merge($p, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        DB::table('permissions')->truncate();
    }
};
