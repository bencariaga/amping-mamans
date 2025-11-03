<?php

namespace App\Actions\IdGeneration;

use App\Models\Operation\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateServiceId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "SERVICE-{$year}";
        $latest = Service::where('service_id', 'like', "{$base}-%")->latest('service_id')->value('service_id');
        $seq = $latest ? (int) Str::substr($latest, -2) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 2, '0');
    }
}
