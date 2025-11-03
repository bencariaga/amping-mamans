<?php

namespace App\Actions\IdGeneration;

use App\Models\User\Member;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateMemberId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "MEMBER-{$year}-{$month}";
        $latest = Member::where('member_id', 'like', "{$base}-%")->latest('member_id')->value('member_id');
        $seq = $latest ? (int) Str::substr($latest, -4) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 4, '0');
    }
}
