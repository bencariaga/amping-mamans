<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\User\ThirdParty;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateThirdPartyId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "TP-{$year}";
        $latest = ThirdParty::where('tp_id', 'like', "{$base}-%")->latest('tp_id')->value('tp_id');
        $seq = $latest ? (int) Str::substr($latest, -3) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 3, '0');
    }
}
