<?php

namespace App\Policies;

use App\Models\Authentication\Account;
use App\Models\User\Client;

class ClientPolicy
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

    public function view(Account $account, Client $client): bool
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

    public function update(Account $account, Client $client): bool
    {
        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
            'Staff',
        ]);
    }

    public function delete(Account $account, Client $client): bool
    {
        $hasActiveApplications = $client->applicants()
            ->whereHas('patients.applications', function ($query) {
                $query->whereIn('application_status', ['Pending', 'Processing', 'Approved']);
            })
            ->exists();

        if ($hasActiveApplications) {
            return false;
        }

        return $account->role && in_array($account->role->role, [
            'Administrator',
            'Manager',
        ]);
    }
}
