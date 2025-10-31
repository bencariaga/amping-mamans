<?php

namespace App\Gates;

use App\Models\Authentication\Account;
use Illuminate\Support\Facades\Gate;

class TariffGate
{
    public static function define(): void
    {
        Gate::define('view-tariffs', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
                'Staff',
                'Viewer',
            ]);
        });

        Gate::define('manage-tariffs', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('create-tariff', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('update-tariff', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('delete-tariff', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('activate-tariff', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('manage-expense-ranges', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });
    }
}
