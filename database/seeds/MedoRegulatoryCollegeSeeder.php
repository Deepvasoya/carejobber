<?php

use Illuminate\Database\Seeder;
use App\Models\Medo\RegulatoryCollege;
use App\Models\Medo\Province;

class MedoRegulatoryCollegeSeeder extends Seeder
{
    public function run()
    {
        $colleges = [
            // Alberta
            ['slug' => 'crna', 'acronym' => 'CRNA', 'name' => 'College of Registered Nurses of Alberta', 'province_slug' => 'ab'],
            ['slug' => 'clpna', 'acronym' => 'CLPNA','name' => 'College of Licensed Practical Nurses of Alberta', 'province_slug' => 'ab'],
            // British Columbia
            ['slug' => 'bccnm', 'acronym' => 'BCCNM','name' => 'BC College of Nurses and Midwives', 'province_slug' => 'bc'],
            // Ontario
            ['slug' => 'cno', 'acronym' => 'CNO', 'name' => 'College of Nurses of Ontario', 'province_slug' => 'on'],
            // Saskatchewan
            ['slug' => 'crns', 'acronym' => 'CRNS', 'name' => 'College of Registered Nurses of Saskatchewan', 'province_slug' => 'sk'],
            ['slug' => 'salpn', 'acronym' => 'SALPN','name' => 'Saskatchewan Association of Licensed Practical Nurses', 'province_slug' => 'sk'],
            // Manitoba
            ['slug' => 'crnm', 'acronym' => 'CRNM', 'name' => 'College of Registered Nurses of Manitoba', 'province_slug' => 'mb'],
            // Nova Scotia
            ['slug' => 'nscn', 'acronym' => 'NSCN', 'name' => 'Nova Scotia College of Nursing', 'province_slug' => 'ns'],
            // Quebec
            ['slug' => 'oiiq', 'acronym' => 'OIIQ', 'name' => 'Ordre des infirmières et infirmiers du Québec', 'province_slug' => 'qc'],
        ];

        foreach ($colleges as $college) {
            $province = Province::where('slug', $college['province_slug'])->first();
            if ($province) {
                RegulatoryCollege::updateOrCreate(['slug' => $college['slug']], [
                    'name' => $college['name'],
                    'acronym' => $college['acronym'],
                    'province_id' => $province->id,
                ]);
            }
        }
    }
}
