<?php

namespace App\Actions\Core\Role;

use Illuminate\Database\Eloquent\Collection;

class FormatRolesWithOptions
{
    public function __construct(
        private GetRoleOptions $getRoleOptions
    ) {}

    public function execute(Collection $roles): Collection
    {
        $optionsA = $this->getRoleOptions->getAllowedActionsOptions();
        $optionsS = $this->getRoleOptions->getAccessScopeOptions();

        return $roles->map(function ($role) use ($optionsA, $optionsS) {
            $allowed = $role->allowed_actions ?? '';
            $scope = $role->access_scope ?? '';

            return [
                'role_id' => $role->role_id,
                'data_id' => $role->data_id,
                'role' => $role->role,
                'allowed_actions' => $allowed,
                'allowed_actions_list' => $this->getRoleOptions->matchOptionsFromString($allowed, $optionsA),
                'access_scope' => $scope,
                'access_scope_list' => $this->getRoleOptions->matchOptionsFromString($scope, $optionsS),
            ];
        });
    }
}
