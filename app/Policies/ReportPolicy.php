<?php

namespace App\Policies;

use App\Models\Authentication\Account;
use App\Models\Audit\Report;

class ReportPolicy
{
    public function viewAny(Account $account): bool
    {
        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
            'Viewer',
        ]);
    }

    public function view(Account $account, Report $report): bool
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

    public function delete(Account $account, Report $report): bool
    {
        return $account->role && $account->role->role === 'Administrator';
    }

    public function export(Account $account, Report $report): bool
    {
        return $this->viewAny($account);
    }

    public function generateFinancialReport(Account $account): bool
    {
        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function generateApplicationReport(Account $account): bool
    {
        return $this->viewAny($account);
    }
}
