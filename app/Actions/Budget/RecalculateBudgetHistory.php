<?php

namespace App\Actions\Budget;

use App\Models\Operation\BudgetUpdate;
use Illuminate\Support\Carbon;

class RecalculateBudgetHistory
{
    public function execute(): void
    {
        $updates = BudgetUpdate::with('data')->get()->sortBy(function ($u) {
            return optional($u->data)->created_at ?: Carbon::create(1970, 1, 1);
        });

        $accum = 0.00;
        $recent = 0.00;

        foreach ($updates as $update) {
            $update->amount_before = $accum;

            if ($update->direction === 'Increase') {
                $accum = (float) $accum + (float) $update->amount_change;
                $recent = (float) $recent + (float) $update->amount_change;
            } else {
                $accum = (float) $accum - (float) $update->amount_change;
                $recent = (float) $recent - (float) $update->amount_change;
            }

            $update->amount_accum = $accum;
            $update->amount_recent = $recent;
            $update->save();
        }
    }
}
