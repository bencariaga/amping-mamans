<?php

namespace App\Policies;

use App\Models\Authentication\Account;
use App\Models\Authentication\Role;

class UserRolePolicy
{
    public function viewAny(Account $account): bool
    {
        return $account->role && $account->role->role === 'Administrator';
    }

    public function view(Account $account, Role $role): bool
    {
        return $this->viewAny($account);
    }

    public function create(Account $account): bool
    {
        return $account->role && $account->role->role === 'Administrator';
    }

    public function update(Account $account, Role $role): bool
    {
        $hasAssignedStaff = $role->accounts()->whereHas('member.staff')->exists();

        if ($hasAssignedStaff && $role->role === 'Administrator') {
            return false;
        }

        return $account->role && $account->role->role === 'Administrator';
    }

    public function delete(Account $account, Role $role): bool
    {
        $hasAssignedStaff = $role->accounts()->whereHas('member.staff')->exists();

        if ($hasAssignedStaff) {
            return false;
        }

        if ($role->role === 'Administrator') {
            return false;
        }

        return $account->role && $account->role->role === 'Administrator';
    }

    public function assignToUser(Account $account): bool
    {
        return $account->role && $account->role->role === 'Administrator';
    }
}
