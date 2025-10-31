<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\Operation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateApplicationId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "APPLICATION-{$year}-{$month}";
        $latest = Application::where('application_id', 'like', "{$base}-%")->latest('application_id')->value('application_id');
        $seq = $latest ? (int) Str::substr($latest, -4) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 4, '0');
    }
}
