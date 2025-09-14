<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembersTableSeeder extends Seeder
{
    public function run()
    {
        $firstNames = [
            'Althea',
            'Isa',
            'Jasmine',
            'Angela',
            'Kristine',
            'Nathalie',
            'Princess',
            'Samantha',
            'Sofia',
            'Maria',
            'Bayani',
            'Leilani',
            'Ligaya',
            'Hiraya',
            'Tala',
            'Amihan',
            'Aurora',
            'Marikit',
            'Dalisay',
            'Ligaya'
        ];

        $middleNames = [
            'Andrea',
            'Angelica',
            'Bernadette',
            'Clarisse',
            'Danica',
            'Evangeline',
            'Florence',
            'Gabrielle',
            'Harriet',
            'Isabel',
            'Joanna',
            'Kathleen',
            'Louise',
            'Michelle',
            'Nicole',
            'Olivia',
            'Patricia',
            'Queenie',
            'Rosa',
            'Sylvia'
        ];

        $lastNames = [
            'Santos',
            'Reyes',
            'Cruz',
            'Bautista',
            'Garcia',
            'Rodriguez',
            'Gonzales',
            'Torres',
            'Ramirez',
            'Flores',
            'Delgado',
            'Medina',
            'Navarro',
            'Lopez',
            'Morales',
            'Estrada',
            'Herrera',
            'Castillo',
            'Silva',
            'Valdez'
        ];

        $suffixes = ['Sr.', 'Jr.', 'II', 'III', 'IV', 'V'];

        $members = [];

        for ($i = 2; $i <= 21; $i++) {
            $idx = $i - 2;
            $fn  = $firstNames[$idx % count($firstNames)];
            $mn  = $middleNames[$idx % count($middleNames)];
            $ln  = $lastNames[$idx % count($lastNames)];
            $sx  = $suffixes[array_rand($suffixes)];

            $members[] = [
                'member_id'   => sprintf("MEMBER-2025-000-000-000-%03d", $i),
                'account_id'  => sprintf("ACCOUNT-2025-000-000-000-%03d", $i),
                'member_type' => 'Client',
                'last_name'   => $ln,
                'first_name'  => $fn,
                'middle_name' => $mn,
                'suffix'      => $sx,
                'username'    => "{$fn} {$mn} {$ln} {$sx}",
            ];
        }

        DB::table('members')->insert($members);
    }
}
