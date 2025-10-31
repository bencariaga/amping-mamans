<?php

namespace App\Actions\Application;

use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;
use Illuminate\Support\Carbon;

class GetLatestActiveTariff
{
    public function execute(string $serviceId): ?TariffList
    {
        $currentDateTime = Carbon::now();

        $candidateTariffs = TariffList::whereHas('expenseRanges', function ($q) use ($serviceId) {
            $q->where('service_id', $serviceId);
        })
            ->where('effectivity_date', '<=', $currentDateTime)
            ->orderBy('effectivity_date', 'desc')
            ->get();

        if ($candidateTariffs->isEmpty()) {
            return null;
        }

        foreach ($candidateTariffs as $tariff) {
            if ($this->isTariffActiveForService($tariff, $serviceId, $candidateTariffs)) {
                return $tariff;
            }
        }

        return null;
    }

    private function isTariffActiveForService(TariffList $tariff, string $serviceId, $allTariffs): bool
    {
        $currentDateTime = Carbon::now();
        $effectivityDate = Carbon::parse($tariff->effectivity_date)->startOfDay();

        if ($effectivityDate->gt($currentDateTime->copy()->startOfDay())) {
            return false;
        }

        $hasValidRanges = ExpenseRange::where('tariff_list_id', $tariff->tariff_list_id)
            ->where('service_id', $serviceId)
            ->whereNotNull('exp_range_min')
            ->whereNotNull('exp_range_max')
            ->whereNotNull('coverage_percent')
            ->where('exp_range_min', '>', 0)
            ->where('exp_range_max', '>', 0)
            ->where('coverage_percent', '>', 0)
            ->count() >= 2;

        if (!$hasValidRanges) {
            return false;
        }

        $latestTariffForService = $allTariffs
            ->filter(function ($tl) use ($serviceId, $currentDateTime) {
                $tlEffDate = Carbon::parse($tl->effectivity_date)->startOfDay();
                if ($tlEffDate->gt($currentDateTime->copy()->startOfDay())) {
                    return false;
                }
                return ExpenseRange::where('tariff_list_id', $tl->tariff_list_id)
                    ->where('service_id', $serviceId)
                    ->exists();
            })
            ->sortByDesc(function ($tl) {
                return Carbon::parse($tl->effectivity_date)->timestamp;
            })
            ->first();

        return $latestTariffForService && $latestTariffForService->tariff_list_id === $tariff->tariff_list_id;
    }
}
