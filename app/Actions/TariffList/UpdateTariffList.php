<?php

namespace App\Actions\TariffList;

use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;
use Illuminate\Support\Facades\DB;

class UpdateTariffList
{
    public function execute(string $tariffListId, array $ranges): TariffList
    {
        return DB::transaction(function () use ($tariffListId, $ranges) {
            $tariffList = TariffList::where('tariff_list_id', $tariffListId)->firstOrFail();

            foreach ($ranges as $range) {
                ExpenseRange::where('exp_range_id', $range['exp_range_id'])
                    ->update([
                        'exp_range_min' => $range['exp_range_min'],
                        'exp_range_max' => $range['exp_range_max'],
                        'coverage_percent' => $range['coverage_percent'],
                    ]);
            }

            $tariffList->touch();

            return $tariffList->fresh();
        });
    }
}
