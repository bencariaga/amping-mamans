<?php

namespace App\Actions\IdGeneration;

use App\Models\Operation\GuaranteeLetter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateGuaranteeLetterId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "GL-{$year}-{$month}";
        $latest = GuaranteeLetter::where('gl_id', 'like', "{$base}-%")->latest('gl_id')->value('gl_id');
        $seq = $latest ? (int) Str::substr($latest, -4) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 4, '0');
    }
}
