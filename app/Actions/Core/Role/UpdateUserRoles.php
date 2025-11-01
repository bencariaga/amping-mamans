<?php

namespace App\Actions\Core\Role;

use Illuminate\Support\Facades\DB;

class UpdateUserRoles
{
    public function execute(array $roles): void
    {
        DB::transaction(function () use ($roles) {
            foreach ($roles as $memberId => $roleId) {
                if (empty($roleId)) {
                    continue;
                }

                DB::table('staff')
                    ->where('member_id', $memberId)
                    ->update(['role_id' => $roleId]);
            }
        });
    }
}
