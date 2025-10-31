<?php

namespace App\Actions\Core;

use App\Models\Authentication\Role;

class UpdateRole
{
    public function execute(string $roleId, array $roleData): Role
    {
        $role = Role::where('role_id', $roleId)->firstOrFail();

        $role->update([
            'role' => $roleData['role'],
            'allowed_actions' => $roleData['allowed_actions'] ?? null,
            'access_scope' => $roleData['access_scope'] ?? null,
        ]);

        return $role->fresh();
    }
}
