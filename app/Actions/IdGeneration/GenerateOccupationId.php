<?php

namespace App\Actions\IdGeneration;

use App\Models\Authentication\Occupation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateOccupationId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "OCCUPATION-{$year}";
        $latest = Occupation::where('occupation_id', 'like', "{$base}-%")->latest('occupation_id')->value('occupation_id');
        $seq = $latest ? (int) Str::substr($latest, -2) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 2, '0');
    }
}
