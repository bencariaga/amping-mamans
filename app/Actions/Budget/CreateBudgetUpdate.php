<?php

namespace App\Actions\Budget;

use App\Actions\IdGeneration\GenerateBudgetUpdateId;
use App\Actions\IdGeneration\GenerateDataId;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Data;
use Illuminate\Support\Facades\DB;

class CreateBudgetUpdate
{
    public function execute(array $budgetData): BudgetUpdate
    {
        return DB::transaction(function () use ($budgetData) {
            $dataId = GenerateDataId::execute();
            $budgetUpdateId = GenerateBudgetUpdateId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $amountSpent = 0.00;
            $amountAccum = 0.00;

            if ($budgetData['direction'] === 'Increase') {
                $amountAccum = (float) $budgetData['amount_change'];
                $amountSpent = 0.00;
            } else {
                $amountSpent = (float) $budgetData['amount_change'];
                $amountAccum = 0.00;
            }

            $payload = [
                'budget_update_id' => $budgetUpdateId,
                'data_id' => $dataId,
                'possessor' => $budgetData['possessor'],
                'amount_accum' => $amountAccum,
                'amount_recent' => $budgetData['amount_recent'] ?? 0.00,
                'amount_before' => $budgetData['amount_before'] ?? 0.00,
                'amount_change' => (float) $budgetData['amount_change'],
                'amount_spent' => $amountSpent,
                'direction' => $budgetData['direction'],
                'reason' => $budgetData['reason'],
            ];

            if ($budgetData['possessor'] === 'Sponsor' && ! empty($budgetData['sponsor_id'])) {
                $payload['sponsor_id'] = $budgetData['sponsor_id'];
            }

            return BudgetUpdate::create($payload);
        });
    }
}
