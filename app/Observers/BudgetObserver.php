<?php

namespace App\Observers;

use App\Models\Operation\BudgetUpdate;
use App\Notifications\LowBudgetNotification;
use App\Models\User\Staff;
use Illuminate\Support\Facades\Log;

class BudgetObserver
{
    private const LOW_BUDGET_THRESHOLD = 50000;

    public function created(BudgetUpdate $budgetUpdate): void
    {
        Log::info('Budget update created', [
            'budget_update_id' => $budgetUpdate->budget_update_id,
            'direction' => $budgetUpdate->direction,
            'amount_change' => $budgetUpdate->amount_change,
            'amount_recent' => $budgetUpdate->amount_recent,
        ]);

        $this->checkLowBudget($budgetUpdate);
    }

    public function updated(BudgetUpdate $budgetUpdate): void
    {
        if ($budgetUpdate->wasChanged('amount_recent')) {
            Log::info('Budget amount changed', [
                'budget_update_id' => $budgetUpdate->budget_update_id,
                'old_amount' => $budgetUpdate->getOriginal('amount_recent'),
                'new_amount' => $budgetUpdate->amount_recent,
            ]);

            $this->checkLowBudget($budgetUpdate);
        }
    }

    public function deleted(BudgetUpdate $budgetUpdate): void
    {
        Log::info('Budget update deleted', [
            'budget_update_id' => $budgetUpdate->budget_update_id,
        ]);
    }

    private function checkLowBudget(BudgetUpdate $budgetUpdate): void
    {
        if ($budgetUpdate->amount_recent <= self::LOW_BUDGET_THRESHOLD) {
            $this->notifyAdministrators($budgetUpdate);
        }
    }

    private function notifyAdministrators(BudgetUpdate $budgetUpdate): void
    {
        try {
            $admins = Staff::whereHas('member.account.role', function ($query) {
                $query->where('role', 'Administrator');
            })->get();

            foreach ($admins as $admin) {
                $admin->member->account->notify(
                    new LowBudgetNotification(
                        $budgetUpdate->amount_recent,
                        self::LOW_BUDGET_THRESHOLD
                    )
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send low budget notification', [
                'budget_update_id' => $budgetUpdate->budget_update_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
