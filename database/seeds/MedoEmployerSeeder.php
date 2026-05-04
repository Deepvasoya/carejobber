<?php

use Illuminate\Database\Seeder;
use App\Models\Medo\Employer;
use App\Models\Medo\Province;

class MedoEmployerSeeder extends Seeder
{
    public function run()
    {
        $ab = Province::where('slug', 'ab')->first();
        if (!$ab) return;

        $employers = [
            ['slug' => 'alberta-health-services', 'name' => 'Alberta Health Services', 'type' => 'public_health', 'province_id' => $ab->id],
            ['slug' => 'covenant-health', 'name' => 'Covenant Health', 'type' => 'public_health', 'province_id' => $ab->id],
            ['slug' => 'bethany-care', 'name' => 'Bethany Care Society', 'type' => 'ltc', 'province_id' => $ab->id],
            ['slug' => 'agecare', 'name' => 'AgeCare', 'type' => 'ltc', 'province_id' => $ab->id],
            ['slug' => 'capitalcare', 'name' => 'CapitalCare', 'type' => 'ltc', 'province_id' => $ab->id],
            ['slug' => 'carewest', 'name' => 'Carewest', 'type' => 'ltc', 'province_id' => $ab->id],
            ['slug' => 'optima-living', 'name' => 'Optima Living', 'type' => 'ltc', 'province_id' => $ab->id],
            ['slug' => 'bayshore', 'name' => 'Bayshore HealthCare', 'type' => 'agency', 'province_id' => $ab->id],
        ];

        foreach ($employers as $employer) {
            Employer::updateOrCreate(['slug' => $employer['slug']], $employer);
        }
    }
}
