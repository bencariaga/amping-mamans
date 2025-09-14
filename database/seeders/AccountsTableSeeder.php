<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountsTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::create('2025', '07', '21', '12', '00', '00');
        $accounts = [];

        for ($i = 2; $i <= 21; $i++) {
            $suffix = str_pad($i, 3, '0', STR_PAD_LEFT);
            $accounts[] = [
                'account_id'          => "ACCOUNT-2025-000-000-000-{$suffix}",
                'data_id'             => "DATA-2025-000-000-000-{$suffix}",
                'account_type'        => 'Person',
                'account_status'      => 'Active',
                'time_registered'     => $now,
                'time_last_deactivated' => null,
                'time_last_reactivated' => null,
            ];
        }

        DB::table('accounts')->insert($accounts);
    }
}
