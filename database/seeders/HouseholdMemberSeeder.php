<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HouseholdMemberSeeder extends Seeder
{
    public function run(): void
    {
        $householdMembers = [
            [
                'household_member_id' => 'HM-2025-AUG-00001',
                'household_id' => 'HOUSEHOLD-2025-AUG-001',
                'client_id' => 'CLIENT-2025-AUG-0001',
                'educational_attainment' => 'College',
                'relationship_to_applicant' => 'Self',
            ],
            [
                'household_member_id' => 'HM-2025-AUG-00002',
                'household_id' => 'HOUSEHOLD-2025-AUG-002',
                'client_id' => 'CLIENT-2025-AUG-0003',
                'educational_attainment' => 'College',
                'relationship_to_applicant' => 'Self',
            ],
        ];

        DB::table('household_members')->insert($householdMembers);
    }
}
