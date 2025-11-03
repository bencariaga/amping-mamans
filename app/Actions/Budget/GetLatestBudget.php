<?php

namespace App\Actions\Budget;

use App\Models\Operation\BudgetUpdate;

class GetLatestBudget
{
    public function execute(): array
    {
        $increases = BudgetUpdate::where('direction', 'Increase')->sum('amount_change');
        $decreases_expenses = BudgetUpdate::where('direction', 'Decrease')->where('reason', '<>', 'Budget Manipulation')->sum('amount_change');
        $manipulations = BudgetUpdate::where('direction', 'Decrease')->where('reason', 'Budget Manipulation')->sum('amount_change');
        $hasSupplementaryBudget = BudgetUpdate::where('reason', 'Supplementary Budget')->exists();
        $allocated = (float) $increases - (float) $manipulations;
        $remaining = $allocated - (float) $decreases_expenses;

        return [
            'amount_accum' => (float) $allocated,
            'amount_change' => (float) $decreases_expenses,
            'amount_recent' => (float) $remaining,
            'has_supplementary_budget' => $hasSupplementaryBudget,
        ];
    }
}
