<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['account_id' => 'ACCOUNT-2025-AUG-0001', 'data_id' => 'DATA-2025-AUG-000000001', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0006', 'data_id' => 'DATA-2025-AUG-000000006', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0007', 'data_id' => 'DATA-2025-AUG-000000007', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0008', 'data_id' => 'DATA-2025-AUG-000000008', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0009', 'data_id' => 'DATA-2025-AUG-000000009', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0010', 'data_id' => 'DATA-2025-AUG-000000010', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0011', 'data_id' => 'DATA-2025-AUG-000000011', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0012', 'data_id' => 'DATA-2025-AUG-000000012', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0013', 'data_id' => 'DATA-2025-AUG-000000013', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0014', 'data_id' => 'DATA-2025-AUG-000000014', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0015', 'data_id' => 'DATA-2025-AUG-000000015', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0016', 'data_id' => 'DATA-2025-AUG-000000016', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0017', 'data_id' => 'DATA-2025-AUG-000000017', 'account_status' => 'Active'],
            ['account_id' => 'ACCOUNT-2025-AUG-0018', 'data_id' => 'DATA-2025-AUG-000000046', 'account_status' => 'Active'],
        ];

        DB::table('accounts')->insert($accounts);
    }
}
