<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $applications = [
            [
                'application_id' => 'APPLICATION-2025-AUG-0001',
                'patient_id' => 'PATIENT-2025-AUG-00008',
                'ap_id' => 'AP-2025-01',
                'exp_range_id' => 'EXP-RANGE-2025-AUG-00002',
                'message_id' => 'MESSAGE-2025-AUG-0003',
                'billed_amount' => 150,
                'apply_at' => '2025-10-28 00:00:00',
            ],
        ];

        DB::table('applications')->insert($applications);
    }
}
