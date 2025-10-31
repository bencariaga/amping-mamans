<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseRangeSeeder extends Seeder
{
    public function run(): void
    {
        // Generate expense ranges for all 6 services with the same pattern
        $expenseRanges = [];
        $services = ['SERVICE-2025-01', 'SERVICE-2025-02', 'SERVICE-2025-03', 'SERVICE-2025-04', 'SERVICE-2025-05', 'SERVICE-2025-06'];
        $rangeCounter = 1;

        foreach ($services as $serviceId) {
            // Define the range patterns
            $ranges = [
                // 10% coverage
                [1, 100, 10], [101, 200, 10], [201, 300, 10],
                // 20% coverage
                [301, 400, 20], [401, 500, 20], [501, 600, 20],
                // 30% coverage
                [601, 700, 30], [701, 800, 30], [801, 900, 30],
                // 40% coverage
                [901, 1000, 40], [1001, 2000, 40], [2001, 3000, 40], [3001, 4000, 40],
                // 50% coverage
                [4001, 5000, 50], [5001, 6000, 50], [6001, 7000, 50], [7001, 8000, 50],
                // 60% coverage
                [8001, 9000, 60], [9001, 10000, 60], [10001, 20000, 60], [20001, 30000, 60],
                // 70% coverage
                [30001, 40000, 70], [40001, 50000, 70], [50001, 60000, 70], [60001, 70000, 70],
                // 80% coverage
                [70001, 80000, 80], [80001, 90000, 80], [90001, 100000, 80], [100001, 200000, 80],
                // 90% coverage
                [200001, 300000, 90], [300001, 400000, 90], [400001, 500000, 90], [500001, 600000, 90],
                // 100% coverage
                [600001, 700000, 100], [700001, 800000, 100], [800001, 900000, 100], [900001, 1000000, 100],
            ];

            foreach ($ranges as $range) {
                $expenseRanges[] = [
                    'exp_range_id' => sprintf('EXP-RANGE-2025-AUG-%05d', $rangeCounter),
                    'tariff_list_id' => 'TL-2025-AUG-1',
                    'service_id' => $serviceId,
                    'exp_range_min' => $range[0],
                    'exp_range_max' => $range[1],
                    'coverage_percent' => $range[2],
                ];
                $rangeCounter++;
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($expenseRanges, 100) as $chunk) {
            DB::table('expense_ranges')->insert($chunk);
        }
    }
}
