<?php

namespace App\Actions\Miscellaneous;

use App\Models\Authentication\Role;
use Illuminate\Database\Eloquent\Collection;

class GetRoles
{
    public static function execute(): Collection
    {
        return Role::join('data', 'roles.data_id', '=', 'data.data_id')
            ->where('data.archive_status', 'Unarchived')
            ->orderBy('data.updated_at', 'desc')
            ->select('roles.*')
            ->get();
    }
}
