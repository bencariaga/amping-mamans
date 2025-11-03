<?php

namespace App\Actions\TariffList;

use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use Illuminate\Support\Facades\Log;

class GetTariffListForView
{
    public function execute(string $tariffListId): array
    {
        $tariffList = TariffList::with(['data'])->where('tariff_list_id', $tariffListId)->firstOrFail();

        $expenseRangesCollection = ExpenseRange::with('service')
            ->where('tariff_list_id', $tariffListId)
            ->get();

        $rangesByService = $expenseRangesCollection
            ->groupBy('service_id')
            ->mapWithKeys(function ($ranges, $serviceId) {
                $service = $ranges->first()->service;
                if (!$service) {
                    Log::warning('Orphaned ExpenseRange found', [
                        'tariff_list_id' => $ranges->first()->tariff_list_id,
                        'service_id' => $serviceId,
                    ]);

                    return [];
                }

                $transformedRanges = $ranges->map(function ($range) use ($service) {
                    return (object) [
                        'service_id' => $service->service_id,
                        'exp_range_id' => $range->exp_range_id,
                        'exp_range_min' => (int) $range->exp_range_min,
                        'exp_range_max' => (int) $range->exp_range_max,
                        'coverage_percent' => (int) $range->coverage_percent,
                    ];
                })->all();

                return [$service->service => $transformedRanges];
            });

        $serviceLists = $rangesByService;
        $serviceTypes = $rangesByService->keys()->all();
        $allServices = Service::all();
        $allServiceTypes = $allServices->pluck('service', 'service_id')->toArray();
        $usedServiceIds = $expenseRangesCollection->pluck('service_id')->unique()->toArray();

        return [
            'tariffListModel' => $tariffList,
            'serviceLists' => $serviceLists,
            'serviceTypes' => $serviceTypes,
            'allServiceTypes' => $allServiceTypes,
            'usedServiceIds' => $usedServiceIds,
        ];
    }
}
