<?php

namespace App\Policies;

use App\Models\Authentication\Account;
use App\Models\Operation\TariffList;

class TariffListVersionPolicy
{
    public function viewAny(Account $account): bool
    {
        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
            'Staff',
            'Viewer',
        ]);
    }

    public function view(Account $account, TariffList $tariffList): bool
    {
        return $this->viewAny($account);
    }

    public function create(Account $account): bool
    {
        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function update(Account $account, TariffList $tariffList): bool
    {
        if ($tariffList->tariff_list_version === 'Active') {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function delete(Account $account, TariffList $tariffList): bool
    {
        if ($tariffList->tariff_list_version === 'Active') {
            return false;
        }

        $hasApplications = $tariffList->expenseRanges()
            ->whereHas('applications')
            ->exists();

        if ($hasApplications) {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function activate(Account $account, TariffList $tariffList): bool
    {
        if ($tariffList->tariff_list_version === 'Active') {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function deactivate(Account $account, TariffList $tariffList): bool
    {
        if ($tariffList->tariff_list_version !== 'Active') {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }
}
