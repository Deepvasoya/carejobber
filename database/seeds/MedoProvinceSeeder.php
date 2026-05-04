<?php

use Illuminate\Database\Seeder;
use App\Models\Medo\Province;

class MedoProvinceSeeder extends Seeder
{
    public function run()
    {
        $provinces = [
            ['slug' => 'ab', 'name' => 'Alberta', 'code' => 'AB', 'is_active' => true],
            ['slug' => 'bc', 'name' => 'British Columbia', 'code' => 'BC', 'is_active' => false],
            ['slug' => 'mb', 'name' => 'Manitoba', 'code' => 'MB', 'is_active' => false],
            ['slug' => 'nb', 'name' => 'New Brunswick', 'code' => 'NB', 'is_active' => false],
            ['slug' => 'nl', 'name' => 'Newfoundland and Labrador', 'code' => 'NL', 'is_active' => false],
            ['slug' => 'ns', 'name' => 'Nova Scotia', 'code' => 'NS', 'is_active' => false],
            ['slug' => 'on', 'name' => 'Ontario', 'code' => 'ON', 'is_active' => false],
            ['slug' => 'pe', 'name' => 'Prince Edward Island', 'code' => 'PE', 'is_active' => false],
            ['slug' => 'qc', 'name' => 'Quebec', 'code' => 'QC', 'is_active' => false],
            ['slug' => 'sk', 'name' => 'Saskatchewan', 'code' => 'SK', 'is_active' => false],
            ['slug' => 'nt', 'name' => 'Northwest Territories', 'code' => 'NT', 'is_active' => false],
            ['slug' => 'nu', 'name' => 'Nunavut', 'code' => 'NU', 'is_active' => false],
            ['slug' => 'yt', 'name' => 'Yukon', 'code' => 'YT', 'is_active' => false],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(['slug' => $province['slug']], $province);
        }
    }
}
