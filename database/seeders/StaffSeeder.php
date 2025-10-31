<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = [
            [
                'staff_id' => 'STAFF-2025-01',
                'member_id' => 'MEMBER-2025-AUG-0001',
                'role_id' => 'ROLE-2025-1',
                'file_name' => null,
                'file_extension' => null,
                'password' => '$2y$10$hdrsmpA1LSXsHsTxJhP2JuiAKUY6px2sPXJ1s4qNo0zqXxGlU4Zvq',
            ],
        ];

        DB::table('staff')->insert($staff);
    }
}
