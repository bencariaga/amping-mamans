<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\User\HouseholdMember;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateHouseholdMemberId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "HM-{$year}-{$month}";
        $latest = HouseholdMember::where('household_member_id', 'like', "{$base}-%")->latest('household_member_id')->value('household_member_id');
        $seq = $latest ? (int) Str::substr($latest, -5) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 5, '0');
    }
}
