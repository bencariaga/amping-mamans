<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Operation\ExpenseRange;
use App\Support\Number;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ExpenseRangeController extends Controller
{
    public function formatNumericValue(string $value): string
    {
        $intValue = (int) $value;
        return Number::format($intValue, 0);
    }

    public function stripZerofill(string $value): string
    {
        return (string) Number::intval($value);
    }

    protected function stripCommas(?string $value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }
        return Str::replace(',', '', $value);
    }

    public function generateExpRangeId()
    {
        return 'EXP'.Str::random(10);
    }

    public function checkOverlap(array $ranges): bool
    {
        $groupedRanges = [];
        foreach ($ranges as $range) {
            $serviceId = $range['service_id'];
            $min = (int) $this->stripCommas($range['exp_range_min'] ?? '0');
            $max = (int) $this->stripCommas($range['exp_range_max'] ?? '0');

            if ($min >= $max) {
                return true;
            }

            if (! isset($groupedRanges[$serviceId])) {
                $groupedRanges[$serviceId] = [];
            }
            $groupedRanges[$serviceId][] = ['min' => $min, 'max' => $max];
        }

        foreach ($groupedRanges as $serviceRangesArray) {
            $serviceRanges = collect($serviceRangesArray)->sortBy('min')->values();

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

    public function validateAndFormatRanges(string $tariffListId, array $ranges): array
    {
        $newRanges = [];
        $processedRanges = [];

        foreach ($ranges as $index => $range) {
            $validator = Validator::make($range, [
                'exp_range_min' => 'required|numeric|min:0',
                'exp_range_max' => 'required|numeric|min:0',
                'coverage_percent' => 'required|numeric|min:0|max:100',
                'service_id' => 'required|string',
                'exp_range_id' => 'required',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'ranges' => [
                        $index => $validator->errors()->toArray(),
                    ],
                ]);
            }

            $serviceId = $range['service_id'];
            $expRangeId = $range['exp_range_id'];
            $minValue = (int) $this->stripCommas($range['exp_range_min']);
            $maxValue = (int) $this->stripCommas($range['exp_range_max']);
            $coverageValue = (int) $this->stripCommas($range['coverage_percent']);

            if ($minValue >= $maxValue) {
                throw ValidationException::withMessages([
                    'ranges' => [
                        $index => [
                            'exp_range_max' => 'Maximum value must be greater than minimum value.',
                        ],
                    ],
                ]);
            }

            $rangeKey = "{$serviceId}-{$minValue}-{$maxValue}";

            if (isset($processedRanges[$rangeKey])) {
                throw ValidationException::withMessages([
                    'ranges' => [
                        $index => [
                            'exp_range_min' => 'This exact range already exists for this service in the submitted list.',
                        ],
                    ],
                ]);
            }

            $processedRanges[$rangeKey] = true;

            $newRanges[] = [
                'exp_range_id' => ($expRangeId === 'new') ? $this->generateExpRangeId() : $expRangeId,
                'tariff_list_id' => $tariffListId,
                'service_id' => $serviceId,
                'exp_range_min' => $minValue,
                'exp_range_max' => $maxValue,
                'coverage_percent' => $coverageValue,
            ];
        }

        if (empty($newRanges)) {
            throw ValidationException::withMessages([
                'ranges' => ['No valid expense ranges were provided.'],
            ]);
        }

        return $newRanges;
    }

    public function updateRangesForTariffList(string $tariffListId, array $ranges): void
    {
        ExpenseRange::where('tariff_list_id', $tariffListId)->delete();
        collect($ranges)->chunk(500)->each(function ($chunk) {
            ExpenseRange::insert($chunk->toArray());
        });
    }
}
