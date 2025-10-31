<?php

namespace App\Gates;

use App\Models\Authentication\Account;
use Illuminate\Support\Facades\Gate;

class UserManagementGate
{
    public static function define(): void
    {
        Gate::define('view-users', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('manage-users', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });

        Gate::define('create-user', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });

        Gate::define('update-user', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });

        Gate::define('delete-user', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });

        Gate::define('deactivate-user', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });

        Gate::define('manage-roles', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });

        Gate::define('assign-role', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });

        Gate::define('view-audit-logs', function (Account $account) {
            return $account->role && $account->role->role === 'Administrator';
        });
    }
}
