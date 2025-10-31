<?php

namespace App\Actions\Budget;

use App\Actions\DatabaseTableIdGeneration\GenerateBudgetUpdateId;
use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Data;
use Illuminate\Support\Facades\DB;

class CreateBudgetForApplication
{
    public function execute(float $assistanceAmount): BudgetUpdate
    {
        return DB::transaction(function () use ($assistanceAmount) {
            $prevBudget = BudgetUpdate::join('data', 'budget_updates.data_id', '=', 'data.data_id')
                ->orderBy('data.created_at', 'desc')
                ->select('budget_updates.*')
                ->first();

            $amount_accum = $prevBudget->amount_accum ?? 0;
            $prevAmountRecent = $prevBudget->amount_recent ?? 0;
            $prevAmountSpent = $prevBudget->amount_spent ?? 0;

            $amount_before = $prevAmountRecent;
            $amount_change = $assistanceAmount;
            $amount_recent = $amount_before - $amount_change;
            $amount_spent = $prevAmountSpent + $amount_change;

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
                'sponsor_id' => null,
                'possessor' => 'AMPING',
                'amount_accum' => $amount_accum,
                'amount_recent' => $amount_recent,
                'amount_before' => $amount_before,
                'amount_change' => $amount_change,
                'amount_spent' => $amount_spent,
                'direction' => 'Decrease',
                'reason' => 'GL Release',
            ]);
        });
    }
}
