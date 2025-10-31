<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BudgetUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $budgetUpdates = [
            [
                'budget_update_id' => 'BDG-UPD-2025-AUG-00001',
                'sponsor_id' => 'SPONSOR-2025-01',
                'possessor' => 'Sponsor',
                'amount_accum' => 600000000,
                'amount_spent' => 0,
                'amount_recent' => 600000000,
                'amount_before' => 500000000,
                'amount_change' => 100000000,
                'direction' => 'Positive',
                'reason' => 'Sponsor Donation',
            ],
            [
                'budget_update_id' => 'BDG-UPD-2025-AUG-00002',
                'sponsor_id' => 'SPONSOR-2025-02',
                'possessor' => 'Sponsor',
                'amount_accum' => 700000000,
                'amount_spent' => 0,
                'amount_recent' => 700000000,
                'amount_before' => 600000000,
                'amount_change' => 100000000,
                'direction' => 'Positive',
                'reason' => 'Sponsor Donation',
            ],
            [
                'budget_update_id' => 'BDG-UPD-2025-AUG-00003',
                'sponsor_id' => 'SPONSOR-2025-03',
                'possessor' => 'Sponsor',
                'amount_accum' => 800000000,
                'amount_spent' => 0,
                'amount_recent' => 800000000,
                'amount_before' => 700000000,
                'amount_change' => 100000000,
                'direction' => 'Positive',
                'reason' => 'Sponsor Donation',
            ],
            [
                'budget_update_id' => 'BDG-UPD-2025-AUG-00004',
                'sponsor_id' => 'SPONSOR-2025-03',
                'possessor' => 'Sponsor',
                'amount_accum' => 900000000,
                'amount_spent' => 0,
                'amount_recent' => 900000000,
                'amount_before' => 800000000,
                'amount_change' => 100000000,
                'direction' => 'Positive',
                'reason' => 'Sponsor Donation',
            ],
            [
                'budget_update_id' => 'BDG-UPD-2025-AUG-00005',
                'sponsor_id' => 'SPONSOR-2025-04',
                'possessor' => 'Sponsor',
                'amount_accum' => 1000000000,
                'amount_spent' => 0,
                'amount_recent' => 1000000000,
                'amount_before' => 900000000,
                'amount_change' => 100000000,
                'direction' => 'Positive',
                'reason' => 'Sponsor Donation',
            ],
        ];

        DB::table('budget_updates')->insert($budgetUpdates);
    }
}
