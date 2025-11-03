<?php

namespace App\Actions\TariffList;

use Illuminate\Http\Request;

class CollectAllRanges
{
    public function execute(Request $request): array
    {
        $ranges = [];

        $rangeMins = $request->input('range_min', []);
        $rangeMaxes = $request->input('range_max', []);
        $coveragePercents = $request->input('tariff_amount', []);

        foreach ($rangeMins as $serviceId => $expRangeMins) {
            foreach ($expRangeMins as $expRangeId => $min) {
                $ranges[] = [
                    'service_id' => $serviceId,
                    'exp_range_id' => $expRangeId,
                    'exp_range_min' => $min,
                    'exp_range_max' => $rangeMaxes[$serviceId][$expRangeId] ?? null,
                    'coverage_percent' => $coveragePercents[$serviceId][$expRangeId] ?? null,
                ];
            }
        }

        return $ranges;
    }
}
