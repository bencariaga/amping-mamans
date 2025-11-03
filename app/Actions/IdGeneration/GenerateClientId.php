<?php

namespace App\Actions\IdGeneration;

use App\Models\User\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateClientId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "CLIENT-{$year}-{$month}";
        $latest = Client::where('client_id', 'like', "{$base}-%")->latest('client_id')->value('client_id');
        $seq = $latest ? (int) Str::substr($latest, -4) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 4, '0');
    }
}
