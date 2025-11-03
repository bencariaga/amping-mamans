<?php

namespace App\Actions\TariffList;

use App\Models\Operation\TariffList;

class CheckEffectivityDate
{
    public function execute(string $date): bool
    {
        return TariffList::where('effectivity_date', $date)->exists();
    }
}
