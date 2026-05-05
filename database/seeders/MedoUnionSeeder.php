<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Medo\Union;
use App\Models\Medo\Province;

class MedoUnionSeeder extends Seeder
{
    public function run()
    {
        $unions = [
            // Alberta
            ['slug' => 'una', 'acronym' => 'UNA', 'name' => 'United Nurses of Alberta', 'province_slug' => 'ab'],
            ['slug' => 'aupe', 'acronym' => 'AUPE', 'name' => 'Alberta Union of Provincial Employees', 'province_slug' => 'ab'],
            ['slug' => 'hsaa', 'acronym' => 'HSAA', 'name' => 'Health Sciences Association of Alberta', 'province_slug' => 'ab'],
            // British Columbia
            ['slug' => 'bcnu', 'acronym' => 'BCNU', 'name' => 'BC Nurses\' Union', 'province_slug' => 'bc'],
            ['slug' => 'heu', 'acronym' => 'HEU', 'name' => 'Hospital Employees\' Union', 'province_slug' => 'bc'],
            // Ontario
            ['slug' => 'ona', 'acronym' => 'ONA', 'name' => 'Ontario Nurses\' Association', 'province_slug' => 'on'],
            ['slug' => 'cupe-on','acronym'=> 'CUPE', 'name' => 'Canadian Union of Public Employees (Ontario)', 'province_slug' => 'on'],
            // Saskatchewan
            ['slug' => 'sun', 'acronym' => 'SUN', 'name' => 'Saskatchewan Union of Nurses', 'province_slug' => 'sk'],
            // Manitoba
            ['slug' => 'mnu', 'acronym' => 'MNU', 'name' => 'Manitoba Nurses Union', 'province_slug' => 'mb'],
            // Atlantic
            ['slug' => 'nsnu', 'acronym' => 'NSNU', 'name' => 'Nova Scotia Nurses\' Union', 'province_slug' => 'ns'],
            ['slug' => 'nbnu', 'acronym' => 'NBNU', 'name' => 'New Brunswick Nurses Union', 'province_slug' => 'nb'],
            ['slug' => 'rnunl', 'acronym' => 'RNUNL','name' => 'Registered Nurses\' Union Newfoundland and Labrador', 'province_slug' => 'nl'],
            // Quebec
            ['slug' => 'fiq', 'acronym' => 'FIQ', 'name' => 'Fédération interprofessionnelle de la santé du Québec', 'province_slug' => 'qc'],
        ];

        foreach ($unions as $union) {
            $province = Province::where('slug', $union['province_slug'])->first();
            if ($province) {
                Union::updateOrCreate(['slug' => $union['slug']], [
                    'name' => $union['name'],
                    'acronym' => $union['acronym'],
                    'province_id' => $province->id,
                ]);
            }
        }
    }
}
