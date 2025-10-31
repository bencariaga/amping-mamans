<?php

namespace App\Gates;

use App\Models\Authentication\Account;
use Illuminate\Support\Facades\Gate;

class BudgetGate
{
    public static function define(): void
    {
        Gate::define('view-budget', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
                'Staff',
                'Viewer',
            ]);
        });

        Gate::define('manage-budget', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('create-budget-update', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('update-budget', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('delete-budget-update', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });

        Gate::define('create-supplementary-budget', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('recalculate-budget', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });
    }
}
