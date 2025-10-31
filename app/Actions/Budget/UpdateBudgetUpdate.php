<?php

namespace App\Actions\Budget;

use App\Models\Operation\BudgetUpdate;

class UpdateBudgetUpdate
{
    public function execute(string $budgetUpdateId, array $updates): BudgetUpdate
    {
        $budgetUpdate = BudgetUpdate::findOrFail($budgetUpdateId);

        if (isset($updates['amount_change']) && is_numeric($updates['amount_change'])) {
            $budgetUpdate->update(['amount_change' => $updates['amount_change']]);
        }

        if (isset($updates['reason'])) {
            $budgetUpdate->update(['reason' => $updates['reason']]);
        }

        return $budgetUpdate->fresh();
    }
}
