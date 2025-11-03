<?php

namespace App\Actions\IdGeneration;

use App\Models\Operation\TariffList;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateTariffListId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "TL-{$year}-{$month}";
        $latest = TariffList::where('tariff_list_id', 'like', "{$base}-%")->latest('tariff_list_id')->value('tariff_list_id');
        $seq = $latest ? (int) Str::afterLast($latest, '-') : 0;
        return "{$base}-".($seq + 1);
    }
}
