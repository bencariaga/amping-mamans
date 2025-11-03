<?php

namespace App\Actions\Role;

use App\Models\Authentication\Role;
use Illuminate\Database\Eloquent\Collection;

class GetRolesWithData
{
    public function execute(): Collection
    {
        return Role::with('data')
            ->join('data', 'roles.data_id', '=', 'data.data_id')
            ->orderBy('data.updated_at', 'desc')
            ->select('roles.*')
            ->get();
    }
}
