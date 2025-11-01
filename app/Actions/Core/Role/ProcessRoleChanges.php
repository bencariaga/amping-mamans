<?php

namespace App\Actions\Core\Role;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessRoleChanges
{
    public function __construct(
        private CreateRole $createRole,
        private UpdateRole $updateRole,
        private DeleteRole $deleteRole
    ) {}

    public function execute(array $creates, array $updates, array $deletes): void
    {
        DB::transaction(function () use ($creates, $updates, $deletes) {
            foreach ($creates as $roleData) {
                if (! is_array($roleData) || empty($roleData['role'])) {
                    continue;
                }

                $roleName = Str::of($roleData['role'])->trim();

                if ($roleName === '') {
                    continue;
                }

                $this->createRole->execute([
                    'role' => (string) $roleName,
                    'allowed_actions' => $roleData['allowed_actions'] ?? null,
                    'access_scope' => $roleData['access_scope'] ?? null,
                ]);
            }

            foreach ($updates as $roleData) {
                if (! is_array($roleData) || empty($roleData['role_id']) || empty($roleData['role'])) {
                    continue;
                }

                $roleName = Str::of($roleData['role'])->trim();

                if ($roleName === '') {
                    continue;
                }

                $this->updateRole->execute($roleData['role_id'], [
                    'role' => (string) $roleName,
                    'allowed_actions' => $roleData['allowed_actions'] ?? null,
                    'access_scope' => $roleData['access_scope'] ?? null,
                ]);
            }

            foreach ($deletes as $roleId) {
                if (! is_string($roleId) || Str::of($roleId)->trim() === '') {
                    continue;
                }

                $this->deleteRole->execute($roleId);
            }
        });
    }
}
