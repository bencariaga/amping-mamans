<?php

namespace App\Actions\ExpenseRange;

use App\Models\Operation\ExpenseRange;

class UpdateRangesForTariffList
{
    public function execute(string $tariffListId, array $ranges): void
    {
        ExpenseRange::where('tariff_list_id', $tariffListId)->delete();
        collect($ranges)->chunk(500)->each(function ($chunk) {
            ExpenseRange::insert($chunk->toArray());
        });
    }
}
