<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TariffListSeeder extends Seeder
{
    public function run(): void
    {
        $tariffLists = [
            ['tariff_list_id' => 'TL-2025-AUG-1', 'data_id' => 'DATA-2025-AUG-000000025', 'tl_status' => 'Active', 'effectivity_date' => '2025-08-02'],
        ];

        DB::table('tariff_lists')->insert($tariffLists);
    }
}
