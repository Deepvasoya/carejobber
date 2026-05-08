<?php

namespace App\Services\Medo;

use App\Models\Medo\Category;
use App\Models\Medo\Province;
use App\Models\Medo\City;
use App\Models\Medo\Job;
use App\Models\Medo\CategoryProvinceSetting;
use Illuminate\Support\Collection;

class ContentGenerator
{
    public function intro(Category $category, Province $province, City $city, Collection $jobs, ?CategoryProvinceSetting $settings): string
    {
        $jobCount = $jobs->count();
        $categoryName = $category->name;
        $cityName = $city->name;
        $provinceName = $province->name;
        
        $salaryInfo = '';
        if ($settings && ($settings->wage_min || $settings->wage_max)) {
            $min = number_format($settings->wage_min ?: $settings->wage_max, 2);
            $max = number_format($settings->wage_max ?: $settings->wage_min, 2);
            $salaryInfo = " Wages typically range from \${$min} to \${$max} per hour.";
        }
        
        $unionInfo = '';
        if ($settings && $settings->union_id) {
            $unionInfo = " Most positions are unionized under {$settings->union->name}.";
        }
        
        return "Find the latest {$categoryName} jobs in {$cityName}, {$provinceName}. We have {$jobCount} active positions available.{$salaryInfo}{$unionInfo} Browse current openings below and apply directly with employers.";
    }

    public function faqs(Category $category, Province $province, City $city, Collection $jobs, ?CategoryProvinceSetting $settings): Collection
    {
        $categoryName = $category->name;
        $cityName = $city->name;
        $provinceName = $province->name;
        $provinceCode = strtoupper($province->code);
        
        $faqs = collect();
        
        // FAQ 1: What does a [category] do?
        $duties = $this->getCategoryDuties($category);
        $faqs->push((object)[
            'question' => "What does a {$categoryName} do?",
            'answer' => "{$categoryName}s {$duties}"
        ]);
        
        // FAQ 2: How much do [category]s make in [city]?
        if ($settings && ($settings->wage_min || $settings->wage_max)) {
            $min = number_format($settings->wage_min ?: $settings->wage_max, 2);
            $max = number_format($settings->wage_max ?: $settings->wage_min, 2);
            $faqs->push((object)[
                'question' => "How much do {$categoryName}s make in {$cityName}?",
                'answer' => "{$categoryName}s in {$cityName} typically earn between \${$min} and \${$max} per hour. Wages vary based on experience, facility type, shift differentials, and whether the position is unionized."
            ]);
        }
        
        // FAQ 3: What qualifications do I need?
        $qualifications = $this->getCategoryQualifications($category, $province);
        $faqs->push((object)[
            'question' => "What qualifications do I need to work as a {$categoryName} in {$provinceName}?",
            'answer' => $qualifications
        ]);
        
        // FAQ 4: Are [category] jobs unionized?
        if ($settings && $settings->union_id) {
            $faqs->push((object)[
                'question' => "Are {$categoryName} jobs in {$cityName} unionized?",
                'answer' => "Most {$categoryName} positions in {$cityName} are unionized under {$settings->union->name}. Union membership provides benefits like standardized wages, job security, and collective bargaining rights."
            ]);
        }
        
        // FAQ 5: What types of facilities hire [category]s?
        $faqs->push((object)[
            'question' => "What types of facilities hire {$categoryName}s in {$cityName}?",
            'answer' => "{$categoryName}s in {$cityName} work in various healthcare settings including acute care hospitals, long-term care facilities, community health centers, home care agencies, and assisted living facilities."
        ]);
        
        // FAQ 6: What shifts are available?
        $faqs->push((object)[
            'question' => "What shifts are available for {$categoryName}s in {$cityName}?",
            'answer' => "{$categoryName} positions in {$cityName} are available across all shifts including days, evenings, nights, and rotating schedules. Many facilities also offer casual, part-time, and full-time positions to accommodate different availability."
        ]);
        
        // FAQ 7: Do I need experience?
        $faqs->push((object)[
            'question' => "Do I need experience to work as a {$categoryName} in {$cityName}?",
            'answer' => "Many {$categoryName} positions in {$cityName} welcome new graduates and provide orientation and training. Some facilities prefer candidates with experience, particularly for specialized units or leadership roles."
        ]);
        
        // FAQ 8: How do I apply?
        $faqs->push((object)[
            'question' => "How do I apply for {$categoryName} jobs in {$cityName}?",
            'answer' => "Browse the active {$categoryName} positions listed on this page. Each job posting includes an 'Apply' button that takes you directly to the employer's application page. Applications are submitted directly to the hiring facility."
        ]);
        
        // FAQ 9: What benefits are typical?
        $faqs->push((object)[
            'question' => "What benefits do {$categoryName}s receive in {$cityName}?",
            'answer' => "Benefits for {$categoryName}s in {$cityName} typically include health and dental coverage, pension plans, paid vacation and sick time, continuing education support, and shift differentials for evenings, nights, and weekends."
        ]);
        
        // FAQ 10: Is there demand for [category]s?
        $faqs->push((object)[
            'question' => "Is there demand for {$categoryName}s in {$cityName}?",
            'answer' => "Yes, {$provinceName} has ongoing demand for qualified {$categoryName}s. The aging population and expansion of healthcare services create consistent job opportunities across {$cityName} and surrounding areas."
        ]);
        
        return $faqs;
    }
    
    private function getCategoryDuties(Category $category): string
    {
        $duties = [
            'hca' => 'provide essential personal care to patients including bathing, dressing, feeding, and mobility assistance. They monitor vital signs, support daily activities, and maintain patient comfort and dignity under the supervision of registered nurses.',
            'lpn' => 'provide direct patient care including administering medications, wound care, monitoring vital signs, and coordinating with the healthcare team. They work under the direction of registered nurses and physicians to deliver safe, quality care.',
            'rn' => 'assess patient conditions, develop care plans, administer treatments and medications, coordinate with healthcare teams, and provide patient and family education. They are responsible for clinical decision-making and ensuring quality patient outcomes.',
        ];
        
        return $duties[$category->slug] ?? 'provide healthcare services to patients in various clinical settings.';
    }
    
    private function getCategoryQualifications(Category $category, Province $province): string
    {
        $provinceCode = strtoupper($province->code);
        
        $qualifications = [
            'hca' => "To work as a Health Care Aide in {$province->name}, you need to complete an approved HCA training program and maintain current CPR and First Aid certification. Registration with the {$province->name} College of Care Aides is required.",
            'lpn' => "To work as an LPN in {$province->name}, you must graduate from an approved practical nursing program and be registered with the College of Licensed Practical Nurses of {$provinceCode}. Current CPR certification is also required.",
            'rn' => "To work as an RN in {$province->name}, you must have a Bachelor of Nursing degree and be registered with the College of Registered Nurses of {$provinceCode}. Current CPR certification and relevant clinical experience are typically required.",
        ];
        
        return $qualifications[$category->slug] ?? "You need appropriate healthcare credentials and registration with the relevant professional college in {$province->name}.";
    }
}
