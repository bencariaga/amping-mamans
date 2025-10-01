<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::create('2025', '07', '21', '12', '00', '00');
        $records = [];

        for ($i = 3; $i <= 22; $i++) {
            $suffix = str_pad($i, 3, '0', STR_PAD_LEFT);
            $records[] = [
                'data_id'     => "DATA-2025-000-000-000-{$suffix}",
                'created_at'  => $now,
                'updated_at'  => $now,
                'archived_at' => null,
                'data_status' => 'Unarchived',
            ];
        }

        DB::table('data')->insert($records);
    }
}
