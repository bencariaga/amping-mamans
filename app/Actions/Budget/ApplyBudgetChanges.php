<?php

namespace App\Actions\Budget;

use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Data;
use Illuminate\Support\Facades\DB;

class ApplyBudgetChanges
{
    public function execute(array $updated, array $deleted): void
    {
        DB::transaction(function () use ($updated, $deleted) {
            foreach ($updated as $item) {
                $budgetUpdate = BudgetUpdate::findOrFail($item['id']);

                if ($budgetUpdate) {
                    $oldAmount = $budgetUpdate->amount_change;
                    $newAmount = $item['amount_change'];
                    $oldReason = $budgetUpdate->reason;
                    $newReason = $item['reason'];

                    if ($oldAmount != $newAmount) {
                        $budgetUpdate->amount_change = $newAmount;
                        $budgetUpdate->save();
                    }

                    if ($oldReason != $newReason) {
                        $budgetUpdate->reason = $newReason;
                        $budgetUpdate->save();
                    }
                }
            }

            foreach ($deleted as $id) {
                $budgetUpdate = BudgetUpdate::find($id);
                if ($budgetUpdate) {
                    $dataId = $budgetUpdate->data_id;
                    $budgetUpdate->delete();
                    Data::where('data_id', $dataId)->delete();
                }
            }
        });
    }
}
