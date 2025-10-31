<?php

namespace App\Policies;

use App\Models\Authentication\Account;
use App\Models\Operation\GuaranteeLetter;

class GLPolicy
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

    public function view(Account $account, GuaranteeLetter $guaranteeLetter): bool
    {
        return $this->viewAny($account);
    }

    public function create(Account $account): bool
    {
        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
            'Staff',
        ]);
    }

    public function update(Account $account, GuaranteeLetter $guaranteeLetter): bool
    {
        if ($guaranteeLetter->gl_status === 'Released') {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function delete(Account $account, GuaranteeLetter $guaranteeLetter): bool
    {
        if ($guaranteeLetter->gl_status === 'Released') {
            return false;
        }

        return $account->role && $account->role->role === 'Administrator';
    }

    public function release(Account $account, GuaranteeLetter $guaranteeLetter): bool
    {
        if ($guaranteeLetter->gl_status !== 'Pending') {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }

    public function print(Account $account, GuaranteeLetter $guaranteeLetter): bool
    {
        return $this->viewAny($account);
    }
}
