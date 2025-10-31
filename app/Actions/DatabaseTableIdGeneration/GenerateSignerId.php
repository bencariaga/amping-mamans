<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\User\Signer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateSignerId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "SIGNER-{$year}";
        $latest = Signer::where('signer_id', 'like', "{$base}-%")->latest('signer_id')->value('signer_id');
        $seq = $latest ? (int) Str::substr($latest, -1) : 0;
        return "{$base}-".($seq + 1);
    }
}
