<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Medo\City;
use App\Models\Medo\Province;

class MedoCitySeeder extends Seeder
{
    public function run()
    {
        $ab = Province::where('slug', 'ab')->first();

        if (!$ab) {
            return;
        }

        $abId = $ab->id;

        $cities = [
            ['slug' => 'calgary', 'name' => 'Calgary', 'province_id' => $abId, 'health_region' => 'Calgary Zone', 'latitude' => 51.0447, 'longitude' => -114.0719],
            ['slug' => 'edmonton', 'name' => 'Edmonton', 'province_id' => $abId, 'health_region' => 'Edmonton Zone', 'latitude' => 53.5461, 'longitude' => -113.4938],
            ['slug' => 'red-deer', 'name' => 'Red Deer', 'province_id' => $abId, 'health_region' => 'Central Zone', 'latitude' => 52.2681, 'longitude' => -113.8112],
            ['slug' => 'lethbridge', 'name' => 'Lethbridge', 'province_id' => $abId, 'health_region' => 'South Zone', 'latitude' => 49.6956, 'longitude' => -112.8326],
            ['slug' => 'medicine-hat', 'name' => 'Medicine Hat', 'province_id' => $abId, 'health_region' => 'South Zone', 'latitude' => 50.0417, 'longitude' => -110.6775],
            ['slug' => 'fort-mcmurray', 'name' => 'Fort McMurray', 'province_id' => $abId, 'health_region' => 'North Zone', 'latitude' => 56.7265, 'longitude' => -111.3803],
            ['slug' => 'grande-prairie', 'name' => 'Grande Prairie', 'province_id' => $abId, 'health_region' => 'North Zone', 'latitude' => 55.1708, 'longitude' => -118.8009],
            ['slug' => 'airdrie', 'name' => 'Airdrie', 'province_id' => $abId, 'health_region' => 'Calgary Zone', 'latitude' => 51.2917, 'longitude' => -114.0144],
            ['slug' => 'st-albert', 'name' => "St. Albert", 'province_id' => $abId, 'health_region' => 'Edmonton Zone', 'latitude' => 53.6305, 'longitude' => -113.6258],
            ['slug' => 'sherwood-park', 'name' => 'Sherwood Park', 'province_id' => $abId, 'health_region' => 'Edmonton Zone', 'latitude' => 53.5255, 'longitude' => -113.2996],
            ['slug' => 'lloydminster', 'name' => 'Lloydminster', 'province_id' => $abId, 'health_region' => 'North Zone', 'latitude' => 53.2807, 'longitude' => -110.0055],
            ['slug' => 'camrose', 'name' => 'Camrose', 'province_id' => $abId, 'health_region' => 'Central Zone', 'latitude' => 53.0173, 'longitude' => -112.8218],
            ['slug' => 'cochrane', 'name' => 'Cochrane', 'province_id' => $abId, 'health_region' => 'Calgary Zone', 'latitude' => 51.1890, 'longitude' => -114.4674],
            ['slug' => 'okotoks', 'name' => 'Okotoks', 'province_id' => $abId, 'health_region' => 'Calgary Zone', 'latitude' => 50.7255, 'longitude' => -113.9749],
            ['slug' => 'spruce-grove', 'name' => 'Spruce Grove', 'province_id' => $abId, 'health_region' => 'Edmonton Zone', 'latitude' => 53.5450, 'longitude' => -113.9009],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(['slug' => $city['slug'], 'province_id' => $city['province_id']], $city);
        }
    }
}
