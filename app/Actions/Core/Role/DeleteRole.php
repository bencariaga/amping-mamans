<?php

namespace App\Actions\Core\Role;

use App\Models\Authentication\Role;
use App\Models\Operation\Data;
use App\Models\User\Staff;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteRole
{
    public function execute(string $roleId): void
    {
        DB::transaction(function () use ($roleId) {
            $role = Role::where('role_id', $roleId)->firstOrFail();

            $staffCount = Staff::where('role_id', $role->role_id)->count();

            if ($staffCount > 0) {
                throw new Exception("Cannot delete role '{$role->role}' because {$staffCount} staff member(s) are assigned to it.");
            }

            $dataId = $role->data_id;
            $role->delete();

            $referencing = DB::table('roles')->where('data_id', $dataId)->exists();

            if (!$referencing) {
                Data::where('data_id', $dataId)->delete();
            }
        });
    }
}
