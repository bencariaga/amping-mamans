<?php

namespace App\Actions\Financial;

use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CalculateTariffStatus
{
    public function execute(TariffList $tariffList, Collection $allTariffLists): array
    {
        $currentDateTime = Carbon::now();
        $effectivityDate = Carbon::parse($tariffList->effectivity_date)->startOfDay();

        $serviceIds = ExpenseRange::where('tariff_list_id', $tariffList->tariff_list_id)
            ->whereNotNull('exp_range_min')
            ->whereNotNull('exp_range_max')
            ->whereNotNull('coverage_percent')
            ->where('exp_range_min', '>', 0)
            ->where('exp_range_max', '>', 0)
            ->where('coverage_percent', '>', 0)
            ->distinct()
            ->pluck('service_id');

        $hasValidRanges = false;
        foreach ($serviceIds as $serviceId) {
            $rangeCount = ExpenseRange::where('tariff_list_id', $tariffList->tariff_list_id)
                ->where('service_id', $serviceId)
                ->whereNotNull('exp_range_min')
                ->whereNotNull('exp_range_max')
                ->whereNotNull('coverage_percent')
                ->where('exp_range_min', '>', 0)
                ->where('exp_range_max', '>', 0)
                ->where('coverage_percent', '>', 0)
                ->count();
            if ($rangeCount >= 2) {
                $hasValidRanges = true;
                break;
            }
        }

        if (!$hasValidRanges) {
            return ['status' => 'Draft', 'color' => 'warning', 'textColor' => 'black'];
        }

        if ($effectivityDate->lte($currentDateTime->copy()->startOfDay())) {
            $tariffServiceIds = ExpenseRange::where('tariff_list_id', $tariffList->tariff_list_id)
                ->distinct()
                ->pluck('service_id')
                ->toArray();

            $hasActiveService = false;
            foreach ($tariffServiceIds as $serviceId) {
                $latestTariffForService = $allTariffLists
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

                if ($latestTariffForService && $latestTariffForService->tariff_list_id === $tariffList->tariff_list_id) {
                    $hasActiveService = true;
                    break;
                }
            }

            if ($hasActiveService) {
                return ['status' => 'Active', 'color' => 'success', 'textColor' => 'white'];
            } else {
                return ['status' => 'Inactive', 'color' => 'secondary', 'textColor' => 'white'];
            }
        } else {
            $hoursUntilEffective = $currentDateTime->diffInHours($effectivityDate, false);

            if ($hoursUntilEffective <= 24) {
                return ['status' => 'Scheduled', 'color' => 'danger', 'textColor' => 'white'];
            } else {
                return ['status' => 'Scheduled', 'color' => 'primary', 'textColor' => 'white'];
            }
        }
    }
}
