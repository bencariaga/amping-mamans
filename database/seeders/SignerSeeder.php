<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SignerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $signers = [
            ['signer_id' => 'SIGNER-2025-1', 'member_id' => 'MEMBER-2025-AUG-0006', 'post_nominal_letters' => null],
            ['signer_id' => 'SIGNER-2025-2', 'member_id' => 'MEMBER-2025-AUG-0007', 'post_nominal_letters' => 'MMPA'],
        ];

        DB::table('signers')->insert($signers);
    }
}
