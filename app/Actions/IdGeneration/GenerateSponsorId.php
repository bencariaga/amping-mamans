<?php

namespace App\Actions\IdGeneration;

use App\Models\User\Sponsor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateSponsorId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "SPONSOR-{$year}";
        $latest = Sponsor::where('sponsor_id', 'like', "{$base}-%")->latest('sponsor_id')->value('sponsor_id');
        $seq = $latest ? (int) Str::substr($latest, -2) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 2, '0');
    }
}
