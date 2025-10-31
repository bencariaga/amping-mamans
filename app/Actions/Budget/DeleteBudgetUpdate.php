<?php

namespace App\Actions\Budget;

use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Data;
use Illuminate\Support\Facades\DB;

class DeleteBudgetUpdate
{
    public function execute(string $budgetUpdateId): void
    {
        DB::transaction(function () use ($budgetUpdateId) {
            $budgetUpdate = BudgetUpdate::findOrFail($budgetUpdateId);
            $dataId = $budgetUpdate->data_id;

            $budgetUpdate->delete();
            Data::where('data_id', $dataId)->delete();
        });
    }
}
