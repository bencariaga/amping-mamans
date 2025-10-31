<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            ['patient_id' => 'PATIENT-2025-AUG-00002', 'client_id' => 'CLIENT-2025-AUG-0003', 'applicant_id' => 'APPLICANT-2025-AUG-0002', 'patient_category' => null],
            ['patient_id' => 'PATIENT-2025-AUG-00003', 'client_id' => 'CLIENT-2025-AUG-0004', 'applicant_id' => 'APPLICANT-2025-AUG-0002', 'patient_category' => 'Senior'],
            ['patient_id' => 'PATIENT-2025-AUG-00004', 'client_id' => 'CLIENT-2025-AUG-0005', 'applicant_id' => 'APPLICANT-2025-AUG-0002', 'patient_category' => 'PWD'],
            ['patient_id' => 'PATIENT-2025-AUG-00006', 'client_id' => 'CLIENT-2025-AUG-0006', 'applicant_id' => 'APPLICANT-2025-AUG-0001', 'patient_category' => null],
            ['patient_id' => 'PATIENT-2025-AUG-00007', 'client_id' => 'CLIENT-2025-AUG-0001', 'applicant_id' => 'APPLICANT-2025-AUG-0001', 'patient_category' => null],
            ['patient_id' => 'PATIENT-2025-AUG-00008', 'client_id' => 'CLIENT-2025-AUG-0007', 'applicant_id' => 'APPLICANT-2025-AUG-0003', 'patient_category' => 'PWD'],
            ['patient_id' => 'PATIENT-2025-AUG-00009', 'client_id' => 'CLIENT-2025-AUG-0008', 'applicant_id' => 'APPLICANT-2025-AUG-0003', 'patient_category' => null],
        ];

        DB::table('patients')->insert($patients);
    }
}
