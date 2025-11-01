<?php

namespace App\Actions\Core\Role;

use App\Models\Authentication\Role;

class CheckRoleDuplication
{
    public function execute(string $roleName, ?string $excludeRoleId = null): bool
    {
        $query = Role::where('role', $roleName);

        if ($excludeRoleId) {
            $query->where('role_id', '!=', $excludeRoleId);
        }

        return $query->exists();
    }
}
