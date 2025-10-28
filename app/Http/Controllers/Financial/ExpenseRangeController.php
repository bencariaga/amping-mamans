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

            if ($min === 0 || $max === 0) {
                continue;
            }

            if ($min >= $max) {
                return true;
            }

            if (! isset($groupedRanges[$serviceId])) {
                $groupedRanges[$serviceId] = [];
            }
            $groupedRanges[$serviceId][] = ['min' => $min, 'max' => $max];
        }

        // Check for duplicates within each service only
        foreach ($groupedRanges as $serviceRangesArray) {
            $serviceRanges = collect($serviceRangesArray)->sortBy('min')->values();

            // Check for exact duplicates first
            for ($i = 0; $i < $serviceRanges->count(); $i++) {
                for ($j = $i + 1; $j < $serviceRanges->count(); $j++) {
                    $current = $serviceRanges[$i];
                    $other = $serviceRanges[$j];
                    
                    // Check for exact duplicate ranges
                    if ($current['min'] === $other['min'] && $current['max'] === $other['max']) {
                        return true;
                    }
                    
                    // Check for duplicate individual values (min or max)
                    if ($current['min'] === $other['min'] || $current['max'] === $other['max']) {
                        return true;
                    }
                    
                    // Check for boundary duplicates (one range's min equals another's max)
                    if ($current['min'] === $other['max'] || $current['max'] === $other['min']) {
                        return true;
                    }
                }
            }

            // Check for traditional overlaps
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
        $serviceRanges = [];

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

            // Initialize service ranges array if not exists
            if (!isset($serviceRanges[$serviceId])) {
                $serviceRanges[$serviceId] = [];
            }

            // Check for exact duplicate ranges
            $rangeKey = "{$serviceId}-{$minValue}-{$maxValue}";
            if (isset($processedRanges[$rangeKey])) {
                throw ValidationException::withMessages([
                    'ranges' => [
                        $index => [
                            'exp_range_min' => 'This exact range already exists for this service.',
                        ],
                    ],
                ]);
            }

            // Check for duplicate individual values within the same service
            foreach ($serviceRanges[$serviceId] as $existingRange) {
                // Check for duplicate minimum values
                if ($existingRange['min'] === $minValue) {
                    throw ValidationException::withMessages([
                        'ranges' => [
                            $index => [
                                'exp_range_min' => 'This minimum value already exists for this service.',
                            ],
                        ],
                    ]);
                }

                // Check for duplicate maximum values
                if ($existingRange['max'] === $maxValue) {
                    throw ValidationException::withMessages([
                        'ranges' => [
                            $index => [
                                'exp_range_max' => 'This maximum value already exists for this service.',
                            ],
                        ],
                    ]);
                }

                // Check for boundary duplicates (one range's min equals another's max)
                if ($existingRange['min'] === $maxValue) {
                    throw ValidationException::withMessages([
                        'ranges' => [
                            $index => [
                                'exp_range_max' => 'This maximum value conflicts with an existing minimum value.',
                            ],
                        ],
                    ]);
                }

                if ($existingRange['max'] === $minValue) {
                    throw ValidationException::withMessages([
                        'ranges' => [
                            $index => [
                                'exp_range_min' => 'This minimum value conflicts with an existing maximum value.',
                            ],
                        ],
                    ]);
                }
            }

            $processedRanges[$rangeKey] = true;
            $serviceRanges[$serviceId][] = ['min' => $minValue, 'max' => $maxValue];

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
