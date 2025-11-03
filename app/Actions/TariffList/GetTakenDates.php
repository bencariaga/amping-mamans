<?php

namespace App\Actions\TariffList;

use App\Models\Operation\TariffList;
use Illuminate\Support\Carbon;

class GetTakenDates
{
    public function execute(): array
    {
        return TariffList::pluck('effectivity_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
    }
}
