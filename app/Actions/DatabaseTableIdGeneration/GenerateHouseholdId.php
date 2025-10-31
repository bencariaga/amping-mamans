<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\User\Household;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateHouseholdId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "HOUSEHOLD-{$year}-{$month}";
        $latest = Household::where('household_id', 'like', "{$base}-%")->latest('household_id')->value('household_id');
        $seq = $latest ? (int) Str::substr($latest, -3) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 3, '0');
    }
}
