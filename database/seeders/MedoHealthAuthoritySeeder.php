<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Medo\HealthAuthority;
use App\Models\Medo\Province;

class MedoHealthAuthoritySeeder extends Seeder
{
    public function run()
    {
        $authorities = [
            // Alberta
            ['slug' => 'ahs', 'acronym' => 'AHS', 'name' => 'Alberta Health Services', 'province_slug' => 'ab', 'careers_url' => 'https://jobs.albertahealthservices.ca'],
            // British Columbia
            ['slug' => 'fraser-health', 'name' => 'Fraser Health', 'province_slug' => 'bc', 'careers_url' => null, 'acronym' => null],
            ['slug' => 'vancouver-coastal', 'name' => 'Vancouver Coastal Health', 'province_slug' => 'bc', 'careers_url' => null, 'acronym' => null],
            ['slug' => 'island-health', 'name' => 'Island Health', 'province_slug' => 'bc', 'careers_url' => null, 'acronym' => null],
            ['slug' => 'interior-health', 'name' => 'Interior Health', 'province_slug' => 'bc', 'careers_url' => null, 'acronym' => null],
            ['slug' => 'northern-health', 'name' => 'Northern Health', 'province_slug' => 'bc', 'careers_url' => null, 'acronym' => null],
            ['slug' => 'phsa', 'name' => 'Provincial Health Services Authority', 'province_slug' => 'bc', 'careers_url' => null, 'acronym' => 'PHSA'],
            // Ontario
            ['slug' => 'ontario-health', 'name' => 'Ontario Health', 'province_slug' => 'on', 'careers_url' => null, 'acronym' => null],
            // Saskatchewan
            ['slug' => 'sha', 'name' => 'Saskatchewan Health Authority', 'province_slug' => 'sk', 'careers_url' => null, 'acronym' => 'SHA'],
            // Manitoba
            ['slug' => 'shared-health-mb', 'name' => 'Shared Health Manitoba', 'province_slug' => 'mb', 'careers_url' => null, 'acronym' => null],
            // Nova Scotia
            ['slug' => 'nshealth', 'name' => 'Nova Scotia Health', 'province_slug' => 'ns', 'careers_url' => null, 'acronym' => null],
            // New Brunswick
            ['slug' => 'horizon-health', 'name' => 'Horizon Health Network', 'province_slug' => 'nb', 'careers_url' => null, 'acronym' => null],
            ['slug' => 'vitalite-health', 'name' => 'Vitalité Health Network', 'province_slug' => 'nb', 'careers_url' => null, 'acronym' => null],
        ];

        foreach ($authorities as $authority) {
            $province = Province::where('slug', $authority['province_slug'])->first();
            if ($province) {
                HealthAuthority::updateOrCreate(['slug' => $authority['slug']], [
                    'name' => $authority['name'],
                    'acronym' => $authority['acronym'],
                    'province_id' => $province->id,
                    'careers_url' => $authority['careers_url'],
                ]);
            }
        }
    }
}
