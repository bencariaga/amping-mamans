<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\Operation\Data;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateDataId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "DATA-{$year}-{$month}";
        $latest = Data::where('data_id', 'like', "{$base}-%")->latest('data_id')->value('data_id');
        $seq = $latest ? (int) Str::substr($latest, -9) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 9, '0');
    }
}
