<?php

namespace App\Actions\Budget;

use App\Actions\IdGeneration\GenerateBudgetUpdateId;
use App\Actions\IdGeneration\GenerateDataId;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Data;
use Illuminate\Support\Facades\DB;

class CreateSupplementaryBudget
{
    public function execute(float $amountChange): BudgetUpdate
    {
        return DB::transaction(function () use ($amountChange) {
            $increases = BudgetUpdate::where('direction', 'Increase')->sum('amount_change');
            $manipulations = BudgetUpdate::where('direction', 'Decrease')->where('reason', 'Budget Manipulation')->sum('amount_change');
            $allocated = (float) $increases - (float) $manipulations;

            $expenses = BudgetUpdate::where('direction', 'Decrease')->where('reason', '<>', 'Budget Manipulation')->sum('amount_change');
            $remaining = $allocated - $expenses;

            $dataId = GenerateDataId::execute();
            $budgetUpdateId = GenerateBudgetUpdateId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return BudgetUpdate::create([
                'budget_update_id' => $budgetUpdateId,
                'data_id' => $dataId,
                'possessor' => 'AMPING',
                'amount_accum' => $allocated + $amountChange,
                'amount_recent' => $remaining + $amountChange,
                'amount_before' => $allocated,
                'amount_change' => $amountChange,
                'amount_spent' => 0.00,
                'direction' => 'Increase',
                'reason' => 'Supplementary Budget',
            ]);
        });
    }
}
