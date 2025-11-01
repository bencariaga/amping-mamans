<?php

namespace App\Actions\ExpenseRange;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ValidateAndFormatExpenseRanges
{
    public function execute(string $tariffListId, array $ranges): array
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

            if (! isset($serviceRanges[$serviceId])) {
                $serviceRanges[$serviceId] = [];
            }

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

            foreach ($serviceRanges[$serviceId] as $existingRange) {
                if ($existingRange['min'] === $minValue) {
                    throw ValidationException::withMessages([
                        'ranges' => [
                            $index => [
                                'exp_range_min' => 'This minimum value already exists for this service.',
                            ],
                        ],
                    ]);
                }

                if ($existingRange['max'] === $maxValue) {
                    throw ValidationException::withMessages([
                        'ranges' => [
                            $index => [
                                'exp_range_max' => 'This maximum value already exists for this service.',
                            ],
                        ],
                    ]);
                }

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

    private function stripCommas(?string $value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        return Str::replace(',', '', $value);
    }

    private function generateExpRangeId(): string
    {
        return 'EXP'.Str::random(10);
    }
}
