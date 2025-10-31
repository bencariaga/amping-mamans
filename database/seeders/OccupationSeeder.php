<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OccupationSeeder extends Seeder
{
    public function run(): void
    {
        $occupations = [
            ['occupation_id' => 'OCCUP-2025-01', 'data_id' => 'DATA-2025-AUG-000000013', 'occupation' => 'Jeepney Driver'],
            ['occupation_id' => 'OCCUP-2025-02', 'data_id' => 'DATA-2025-AUG-000000014', 'occupation' => 'Tricycle Driver'],
            ['occupation_id' => 'OCCUP-2025-03', 'data_id' => 'DATA-2025-AUG-000000015', 'occupation' => 'Pedicab Driver'],
            ['occupation_id' => 'OCCUP-2025-04', 'data_id' => 'DATA-2025-AUG-000000016', 'occupation' => 'Construction Worker'],
            ['occupation_id' => 'OCCUP-2025-05', 'data_id' => 'DATA-2025-AUG-000000017', 'occupation' => 'Factory Worker'],
            ['occupation_id' => 'OCCUP-2025-06', 'data_id' => 'DATA-2025-AUG-000000018', 'occupation' => 'Warehouse Worker'],
            ['occupation_id' => 'OCCUP-2025-07', 'data_id' => 'DATA-2025-AUG-000000019', 'occupation' => 'Farmer'],
            ['occupation_id' => 'OCCUP-2025-08', 'data_id' => 'DATA-2025-AUG-000000020', 'occupation' => 'Fisherperson'],
            ['occupation_id' => 'OCCUP-2025-09', 'data_id' => 'DATA-2025-AUG-000000021', 'occupation' => 'Janitor'],
            ['occupation_id' => 'OCCUP-2025-10', 'data_id' => 'DATA-2025-AUG-000000022', 'occupation' => 'Teacher'],
            ['occupation_id' => 'OCCUP-2025-11', 'data_id' => 'DATA-2025-AUG-000000023', 'occupation' => 'Student'],
            ['occupation_id' => 'OCCUP-2025-12', 'data_id' => 'DATA-2025-AUG-000000024', 'occupation' => 'Security Guard'],
            ['occupation_id' => 'OCCUP-2025-13', 'data_id' => 'DATA-2025-AUG-000000038', 'occupation' => 'Waiter'],
            ['occupation_id' => 'OCCUP-2025-14', 'data_id' => 'DATA-2025-AUG-000000041', 'occupation' => 'Artist'],
        ];

        DB::table('occupations')->insert($occupations);
    }
}
