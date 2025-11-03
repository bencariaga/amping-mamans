<?php

namespace App\Actions\IdGeneration;

use App\Models\Authentication\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateRoleId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "ROLE-{$year}";
        $latest = Role::where('role_id', 'like', "{$base}-%")->latest('role_id')->value('role_id');
        $seq = $latest ? (int) Str::substr($latest, -1) : 0;
        return "{$base}-".($seq + 1);
    }
}
