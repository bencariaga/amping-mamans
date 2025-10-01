<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientsTableSeeder extends Seeder
{
    public function run()
    {
        $birthdate    = '2000-01-01';
        $civilStatus  = 'Single';
        $jobStatus    = 'Unemployed';
        $occupation   = null;
        $monthlyIncome = 10000.00;

        $sexes = [
            'Female',
            'Female',
            'Female',
            'Female',
            'Female',
            'Female',
            'Female',
            'Female',
            'Female',
            'Female',
            'Male',
            'Male',
            'Male',
            'Male',
            'Male',
            'Female',
            'Female',
            'Female',
            'Female',
            'Female',
        ];

        $clients = [];
        for ($i = 1; $i <= 20; $i++) {
            $clientSuffix = str_pad($i, 3, '0', STR_PAD_LEFT);
            $memberSuffix = str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            $clients[] = [
                'client_id'    => "CLIENT-2025-000-000-000-{$clientSuffix}",
                'member_id'    => "MEMBER-2025-000-000-000-{$memberSuffix}",
                'client_type'  => 'Applicant',
                'birthdate'    => $birthdate,
                'sex'          => $sexes[$i - 1],
                'civil_status' => $civilStatus,
                'job_status'   => $jobStatus,
                'occupation'   => $occupation,
                'monthly_income' => $monthlyIncome,
            ];
        }

        DB::table('clients')->insert($clients);
    }
}
