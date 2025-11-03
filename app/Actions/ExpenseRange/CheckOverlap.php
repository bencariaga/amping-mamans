<?php

namespace App\Actions\ExpenseRange;

use Illuminate\Support\Str;

class CheckOverlap
{
    public function execute(array $ranges): bool
    {
        $groupedRanges = [];

        foreach ($ranges as $range) {
            $serviceId = $range['service_id'];
            $min = (int) $this->stripCommas($range['exp_range_min'] ?? '0');
            $max = (int) $this->stripCommas($range['exp_range_max'] ?? '0');

            if ($min === 0 || $max === 0) {
                continue;
            }

            if ($min >= $max) {
                return true;
            }

            if (!isset($groupedRanges[$serviceId])) {
                $groupedRanges[$serviceId] = [];
            }

            $groupedRanges[$serviceId][] = ['min' => $min, 'max' => $max];
        }

        foreach ($groupedRanges as $serviceRangesArray) {
            $serviceRanges = collect($serviceRangesArray)->sortBy('min')->values();

            for ($i = 0; $i < $serviceRanges->count(); $i++) {
                for ($j = $i + 1; $j < $serviceRanges->count(); $j++) {
                    $current = $serviceRanges[$i];
                    $other = $serviceRanges[$j];

                    if ($current['min'] === $other['min'] && $current['max'] === $other['max']) {
                        return true;
                    }
                    if ($current['min'] === $other['min'] || $current['max'] === $other['max']) {
                        return true;
                    }

                    if ($current['min'] === $other['max'] || $current['max'] === $other['min']) {
                        return true;
                    }
                }
            }

            for ($i = 0; $i < $serviceRanges->count() - 1; $i++) {
                $current = $serviceRanges[$i];
                $next = $serviceRanges[$i + 1];

                if ($current['max'] > $next['min']) {
                    return true;
                }
            }
        }

        return false;
    }

    private function stripCommas(?string $value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }
        return Str::replace(',', '', $value);
    }
}
