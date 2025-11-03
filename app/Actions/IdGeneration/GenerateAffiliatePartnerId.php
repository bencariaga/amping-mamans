<?php

namespace App\Actions\IdGeneration;

use App\Models\User\AffiliatePartner;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateAffiliatePartnerId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "AP-{$year}";
        $latest = AffiliatePartner::where('ap_id', 'like', "{$base}-%")->latest('ap_id')->value('ap_id');
        $seq = $latest ? (int) Str::substr($latest, -2) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 2, '0');
    }
}
