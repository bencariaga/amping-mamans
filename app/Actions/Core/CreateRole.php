<?php

namespace App\Actions\Core;

use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Actions\DatabaseTableIdGeneration\GenerateRoleId;
use App\Models\Authentication\Role;
use App\Models\Operation\Data;
use Illuminate\Support\Facades\DB;

class CreateRole
{
    public function execute(array $roleData): Role
    {
        return DB::transaction(function () use ($roleData) {
            $dataId = GenerateDataId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Role::create([
                'role_id' => GenerateRoleId::execute(),
                'data_id' => $dataId,
                'role' => $roleData['role'],
                'allowed_actions' => $roleData['allowed_actions'] ?? null,
                'access_scope' => $roleData['access_scope'] ?? null,
            ]);
        });
    }
}
