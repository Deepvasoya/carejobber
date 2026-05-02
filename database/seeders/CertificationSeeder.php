<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Certification;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $certifications = [
            'CPR Certified',
            'First Aid Certified',
            'Basic Life Support (BLS)',
            'Advanced Cardiac Life Support (ACLS)',
            'Pediatric Advanced Life Support (PALS)',
            'Registered Nurse (RN) License',
            'Licensed Practical Nurse (LPN)',
            'Certified Nursing Assistant (CNA)',
            'Medical Assistant Certification',
            'Phlebotomy Certification',
            'EKG Technician Certification',
            'Pharmacy Technician Certification',
            'Certified Medical Coder (CPC)',
            'Healthcare Administrator Certification',
            'Patient Care Technician (PCT)',
            'Emergency Medical Technician (EMT)',
            'Paramedic Certification',
            'Dental Assistant Certification',
            'Physical Therapy Assistant License',
            'Occupational Therapy Assistant License'
        ];

        foreach ($certifications as $index => $certName) {
            Certification::create([
                'name' => $certName,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
