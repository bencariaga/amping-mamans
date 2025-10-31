<?php

namespace App\Policies;

use App\Models\Authentication\Account;
use App\Models\Operation\Application;

class ApplicationPolicy
{
    public function viewAny(Account $account): bool
    {
        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
            'Staff',
            'Encoder',
            'Viewer',
        ]);
    }

    public function view(Account $account, Application $application): bool
    {
        return $this->viewAny($account);
    }

    public function create(Account $account): bool
    {
        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
            'Staff',
            'Encoder',
        ]);
    }

    public function update(Account $account, Application $application): bool
    {
        if ($application->application_status === 'Completed') {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
            'Staff',
        ]);
    }

    public function delete(Account $account, Application $application): bool
    {
        if (in_array($application->application_status, ['Completed', 'Processing'])) {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function approve(Account $account, Application $application): bool
    {
        if ($application->application_status !== 'Pending') {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function reject(Account $account, Application $application): bool
    {
        if ($application->application_status !== 'Pending') {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }
}
