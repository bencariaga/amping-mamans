<?php

namespace App\Gates;

use App\Models\Authentication\Account;
use Illuminate\Support\Facades\Gate;

class ApplicationGate
{
    public static function define(): void
    {
        Gate::define('view-applications', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
                'Staff',
                'Encoder',
                'Viewer',
            ]);
        });

        Gate::define('create-application', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
                'Staff',
                'Encoder',
            ]);
        });

        Gate::define('update-application', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
                'Staff',
            ]);
        });

        Gate::define('delete-application', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('approve-application', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('reject-application', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
            ]);
        });

        Gate::define('process-application', function (Account $account) {
            return $account->role && in_array($account->role->role, [
                'Administrator',
                'Manager',
                'Staff',
            ]);
        });
    }
}
