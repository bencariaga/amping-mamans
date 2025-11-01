<?php

namespace App\Actions\Core\Role;

use App\Models\Authentication\Role;
use InvalidArgumentException;

class UpdateRole
{
    public function __construct(
        private CheckRoleDuplication $checkRoleDuplication
    ) {}
    public function execute(string $roleId, array $roleData): Role
    {
        if ($this->checkRoleDuplication->execute($roleData['role'], $roleId)) {
            throw new InvalidArgumentException('A role with this name already exists.');
        }

        $role = Role::where('role_id', $roleId)->firstOrFail();

        $role->update([
            'role' => $roleData['role'],
            'allowed_actions' => $roleData['allowed_actions'] ?? null,
            'access_scope' => $roleData['access_scope'] ?? null,
        ]);

        return $role->fresh();
    }
}
