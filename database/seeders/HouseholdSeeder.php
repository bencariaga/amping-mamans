<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HouseholdSeeder extends Seeder
{
    public function run(): void
    {
        $households = [
            ['household_id' => 'HOUSEHOLD-2025-AUG-001', 'household_name' => 'Cariaga'],
            ['household_id' => 'HOUSEHOLD-2025-AUG-002', 'household_name' => 'Carreon'],
        ];

        DB::table('households')->insert($households);
    }
}
