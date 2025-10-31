<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\User\Staff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateStaffId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "STAFF-{$year}";
        $latest = Staff::where('staff_id', 'like', "{$base}-%")->latest('staff_id')->value('staff_id');
        $seq = $latest ? (int) Str::substr($latest, -2) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 2, '0');
    }
}
