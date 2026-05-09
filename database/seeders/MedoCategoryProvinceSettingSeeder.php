<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Medo\Category;
use App\Models\Medo\Province;
use App\Models\Medo\Union;
use App\Models\Medo\RegulatoryCollege;
use App\Models\Medo\CategoryProvinceSetting;

class MedoCategoryProvinceSettingSeeder extends Seeder
{
    public function run()
    {
        $ab = Province::where('slug', 'ab')->first();
        if (!$ab) return;

        $hca = Category::where('slug', 'hca')->first();
        $lpn = Category::where('slug', 'lpn')->first();
        $rn = Category::where('slug', 'rn')->first();

        $aupe = Union::where('slug', 'aupe')->first();
        $una = Union::where('slug', 'una')->first();

        $clpna = RegulatoryCollege::where('slug', 'clpna')->first();
        $crna = RegulatoryCollege::where('slug', 'crna')->first();

        $settings = [
            [
                'category_id' => $hca->id,
                'province_id' => $ab->id,
                'union_id' => $aupe ? $aupe->id : null,
                'regulatory_college_id' => null, // HCA Directory, not a college
                'wage_min' => 26.42,
                'wage_max' => 29.58,
                'pension_plan' => 'LAPP',
                'shift_premium_evening' => 2.75,
                'shift_premium_night' => 5.50,
                'certification_requirements' => 'Provincial HCA certification through the Alberta Health Care Aide Directory is required for AHS and most LTC employers. Graduates of approved programs at NorQuest, Bow Valley College, SAIT, and Robertson College qualify.',
                'training_pathways' => 'NorQuest College (Edmonton), Bow Valley College (Calgary), SAIT, Robertson College, and Lethbridge Polytechnic offer approved HCA programs of 5–7 months.',
                'ien_pathway' => 'Internationally trained care workers must apply to the Alberta Health Care Aide Directory and may need bridging coursework. Many employers including AHS and major LTC operators recruit IEHCA graduates.',
            ],
            [
                'category_id' => $lpn->id,
                'province_id' => $ab->id,
                'union_id' => $aupe ? $aupe->id : null,
                'regulatory_college_id' => $clpna ? $clpna->id : null,
                'wage_min' => 33.52,
                'wage_max' => 43.85,
                'pension_plan' => 'LAPP',
                'shift_premium_evening' => 2.75,
                'shift_premium_night' => 5.50,
                'certification_requirements' => 'Active practice permit with the College of Licensed Practical Nurses of Alberta (CLPNA) is required. Graduates of approved 2-year practical nursing diplomas qualify to write the CPNRE.',
                'training_pathways' => 'Bow Valley College (Calgary), NorQuest (Edmonton), NAIT, and Lethbridge Polytechnic offer 2-year Practical Nurse diplomas.',
                'ien_pathway' => 'Internationally educated nurses apply to CLPNA, who assess credentials and may require the SEC examination, bridging programs, or supervised practice.',
            ],
            [
                'category_id' => $rn->id,
                'province_id' => $ab->id,
                'union_id' => $una ? $una->id : null,
                'regulatory_college_id' => $crna ? $crna->id : null,
                'wage_min' => 42.84,
                'wage_max' => 58.64,
                'pension_plan' => 'LAPP',
                'shift_premium_evening' => 2.75,
                'shift_premium_night' => 5.75,
                'certification_requirements' => 'Active registration with the College of Registered Nurses of Alberta (CRNA) is required. BScN graduates from approved Alberta programs qualify to write the NCLEXRN.',
                'training_pathways' => 'University of Alberta, University of Calgary, MacEwan University, Mount Royal University, and Athabasca University offer approved BScN programs.',
                'ien_pathway' => 'Internationally educated nurses must complete the NNAS assessment, then apply to CRNA. Bridging programs are available at Mount Royal and other institutions.',
            ],
        ];

        foreach ($settings as $setting) {
            CategoryProvinceSetting::updateOrCreate([
                'category_id' => $setting['category_id'],
                'province_id' => $setting['province_id']
            ], $setting);
        }
    }
}
