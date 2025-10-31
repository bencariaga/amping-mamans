<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['client_id' => 'CLIENT-2025-AUG-0001', 'member_id' => 'MEMBER-2025-AUG-0008', 'occupation_id' => 'OCCUP-2025-11', 'birthdate' => '2003-04-02', 'age' => 22, 'sex' => 'Male', 'civil_status' => 'Single', 'monthly_income' => 10000],
            ['client_id' => 'CLIENT-2025-AUG-0003', 'member_id' => 'MEMBER-2025-AUG-0010', 'occupation_id' => 'OCCUP-2025-13', 'birthdate' => '2003-04-02', 'age' => 22, 'sex' => 'Male', 'civil_status' => 'Single', 'monthly_income' => 10000],
            ['client_id' => 'CLIENT-2025-AUG-0004', 'member_id' => 'MEMBER-2025-AUG-0011', 'occupation_id' => null, 'birthdate' => null, 'age' => 100, 'sex' => 'Male', 'civil_status' => null, 'monthly_income' => null],
            ['client_id' => 'CLIENT-2025-AUG-0005', 'member_id' => 'MEMBER-2025-AUG-0012', 'occupation_id' => null, 'birthdate' => null, 'age' => 40, 'sex' => 'Male', 'civil_status' => null, 'monthly_income' => null],
            ['client_id' => 'CLIENT-2025-AUG-0006', 'member_id' => 'MEMBER-2025-AUG-0013', 'occupation_id' => null, 'birthdate' => null, 'age' => 22, 'sex' => 'Female', 'civil_status' => null, 'monthly_income' => null],
            ['client_id' => 'CLIENT-2025-AUG-0007', 'member_id' => 'MEMBER-2025-AUG-0014', 'occupation_id' => 'OCCUP-2025-10', 'birthdate' => '2000-01-01', 'age' => 25, 'sex' => 'Female', 'civil_status' => 'Single', 'monthly_income' => 15000],
            ['client_id' => 'CLIENT-2025-AUG-0008', 'member_id' => 'MEMBER-2025-AUG-0015', 'occupation_id' => null, 'birthdate' => null, 'age' => 22, 'sex' => 'Male', 'civil_status' => null, 'monthly_income' => null],
        ];

        DB::table('clients')->insert($clients);
    }
}
