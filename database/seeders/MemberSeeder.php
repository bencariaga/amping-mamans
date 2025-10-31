<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            ['member_id' => 'MEMBER-2025-AUG-0001', 'account_id' => 'ACCOUNT-2025-AUG-0001', 'member_type' => 'Staff', 'last_name' => 'Cariaga', 'middle_name' => 'Leproso', 'first_name' => 'Benhur', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0002', 'account_id' => 'ACCOUNT-2025-AUG-0010', 'member_type' => 'Third-Party', 'last_name' => 'Al-Alawi', 'middle_name' => 'Marbella', 'first_name' => 'Mariam', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0003', 'account_id' => 'ACCOUNT-2025-AUG-0011', 'member_type' => 'Third-Party', 'last_name' => 'Harake', 'middle_name' => 'Ocampo', 'first_name' => 'Zeinab', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0004', 'account_id' => 'ACCOUNT-2025-AUG-0012', 'member_type' => 'Third-Party', 'last_name' => 'Otwell', 'middle_name' => null, 'first_name' => 'Taylor', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0005', 'account_id' => 'ACCOUNT-2025-AUG-0013', 'member_type' => 'Third-Party', 'last_name' => 'Pacquiao', 'middle_name' => 'Dapidran', 'first_name' => 'Emmanuel', 'suffix' => 'Sr.'],
            ['member_id' => 'MEMBER-2025-AUG-0006', 'account_id' => 'ACCOUNT-2025-AUG-0014', 'member_type' => 'Signer', 'last_name' => 'Pacquiao', 'middle_name' => 'Geronimo', 'first_name' => 'Lorelie', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0007', 'account_id' => 'ACCOUNT-2025-AUG-0015', 'member_type' => 'Signer', 'last_name' => 'Ambuang', 'middle_name' => 'Dapidran', 'first_name' => 'Maritess', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0008', 'account_id' => 'ACCOUNT-2025-AUG-0016', 'member_type' => 'Client', 'last_name' => 'Cariaga', 'middle_name' => 'Leproso', 'first_name' => 'Benhur', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0010', 'account_id' => 'ACCOUNT-2025-AUG-0017', 'member_type' => 'Client', 'last_name' => 'Carreon', 'middle_name' => 'Ledesma', 'first_name' => 'Benjamin', 'suffix' => 'Jr.'],
            ['member_id' => 'MEMBER-2025-AUG-0011', 'account_id' => 'ACCOUNT-2025-AUG-0017', 'member_type' => 'Client', 'last_name' => 'Rivera', 'middle_name' => 'Ibarra', 'first_name' => 'Simoun', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0012', 'account_id' => 'ACCOUNT-2025-AUG-0017', 'member_type' => 'Client', 'last_name' => 'Saavedra', 'middle_name' => null, 'first_name' => 'Julito', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0013', 'account_id' => 'ACCOUNT-2025-AUG-0016', 'member_type' => 'Client', 'last_name' => 'Hinoctan', 'middle_name' => 'Provendido', 'first_name' => 'Angel', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0014', 'account_id' => 'ACCOUNT-2025-AUG-0018', 'member_type' => 'Client', 'last_name' => 'Castañeda', 'middle_name' => null, 'first_name' => 'Key', 'suffix' => null],
            ['member_id' => 'MEMBER-2025-AUG-0015', 'account_id' => 'ACCOUNT-2025-AUG-0018', 'member_type' => 'Client', 'last_name' => 'Cariaga', 'middle_name' => null, 'first_name' => 'Benhur', 'suffix' => null],
        ];

        DB::table('members')->insert($members);
    }
}
