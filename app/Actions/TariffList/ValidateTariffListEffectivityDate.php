<?php

namespace App\Actions\TariffList;

use App\Exceptions\FinanceException;
use App\Models\Operation\TariffList;
use Illuminate\Support\Carbon;

class ValidateTariffListEffectivityDate
{
    public function execute(string $date): void
    {
        $effectivityDate = Carbon::parse($date);
        $tomorrow = Carbon::tomorrow()->startOfDay();

        if ($effectivityDate->lt($tomorrow)) {
            throw FinanceException::invalidEffectivityDate($date);
        }

        if (TariffList::where('effectivity_date', $date)->exists()) {
            throw FinanceException::effectivityDateTaken($date);
        }
    }
}
