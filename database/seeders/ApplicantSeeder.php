<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        $applicants = [
            [
                'applicant_id' => 'APPLICANT-2025-AUG-0001',
                'client_id' => 'CLIENT-2025-AUG-0001',
                'province' => 'South Cotabato',
                'city' => 'General Santos',
                'municipality' => 'N / A',
                'barangay' => 'Labangal',
                'subdivision' => 'Doña Soledad',
                'purok' => null,
                'sitio' => null,
                'street' => null,
                'phase' => null,
                'block_number' => null,
                'house_number' => null,
                'job_status' => 'Casual',
                'house_occupation_status' => 'Renter',
                'lot_occupation_status' => 'Renter',
                'phic_affiliation' => 'Affiliated',
                'phic_category' => 'Employed',
                'is_also_patient' => 'yes',
                'patient_quantity' => 2,
            ],
            [
                'applicant_id' => 'APPLICANT-2025-AUG-0002',
                'client_id' => 'CLIENT-2025-AUG-0003',
                'province' => 'South Cotabato',
                'city' => 'General Santos',
                'municipality' => 'N / A',
                'barangay' => 'Labangal',
                'subdivision' => null,
                'purok' => null,
                'sitio' => null,
                'street' => null,
                'phase' => null,
                'block_number' => null,
                'house_number' => null,
                'job_status' => 'Casual',
                'house_occupation_status' => 'Owner',
                'lot_occupation_status' => 'Owner',
                'phic_affiliation' => 'Affiliated',
                'phic_category' => 'Sponsored / Indigent',
                'is_also_patient' => 'yes',
                'patient_quantity' => 3,
            ],
            [
                'applicant_id' => 'APPLICANT-2025-AUG-0003',
                'client_id' => 'CLIENT-2025-AUG-0007',
                'province' => 'South Cotabato',
                'city' => 'General Santos',
                'municipality' => 'N / A',
                'barangay' => 'City Heights',
                'subdivision' => null,
                'purok' => null,
                'sitio' => null,
                'street' => 'Daproza',
                'phase' => null,
                'block_number' => null,
                'house_number' => null,
                'job_status' => 'Casual',
                'house_occupation_status' => 'Owner',
                'lot_occupation_status' => 'Owner',
                'phic_affiliation' => 'Affiliated',
                'phic_category' => 'Employed',
                'is_also_patient' => 'yes',
                'patient_quantity' => 2,
            ],
        ];

        DB::table('applicants')->insert($applicants);
    }
}
