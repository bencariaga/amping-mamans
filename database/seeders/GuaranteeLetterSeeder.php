<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuaranteeLetterSeeder extends Seeder
{
    public function run(): void
    {
        $guaranteeLetters = [
            [
                'gl_id' => 'GL-2025-AUG-0001',
                'data_id' => 'DATA-2025-AUG-000000026',
                'application_id' => 'APPLICATION-2025-AUG-0001',
                'sponsor_id' => 'SPONSOR-2025-01',
                'is_sponsored' => 'Yes',
            ],
        ];

        DB::table('guarantee_letters')->insert($guaranteeLetters);
    }
}
