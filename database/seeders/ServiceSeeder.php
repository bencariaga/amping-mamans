<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['service_id' => 'SERVICE-2025-01', 'data_id' => 'DATA-2025-AUG-000000004', 'service' => 'Hospital Bill'],
            ['service_id' => 'SERVICE-2025-02', 'data_id' => 'DATA-2025-AUG-000000005', 'service' => 'Medical Prescription'],
            ['service_id' => 'SERVICE-2025-03', 'data_id' => 'DATA-2025-AUG-000000006', 'service' => 'Laboratory Test'],
            ['service_id' => 'SERVICE-2025-04', 'data_id' => 'DATA-2025-AUG-000000007', 'service' => 'Diagnostic Test'],
            ['service_id' => 'SERVICE-2025-05', 'data_id' => 'DATA-2025-AUG-000000008', 'service' => 'Hemodialysis'],
            ['service_id' => 'SERVICE-2025-06', 'data_id' => 'DATA-2025-AUG-000000009', 'service' => 'Blood Request'],
        ];

        DB::table('services')->insert($services);
    }
}
