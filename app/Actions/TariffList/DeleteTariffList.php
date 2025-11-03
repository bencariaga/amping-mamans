<?php

namespace App\Actions\TariffList;

use App\Models\Operation\Data;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;
use Illuminate\Support\Facades\DB;

class DeleteTariffList
{
    public function execute(string $tariffListId): void
    {
        DB::transaction(function () use ($tariffListId) {
            $tariffList = TariffList::where('tariff_list_id', $tariffListId)->firstOrFail();
            $dataId = $tariffList->data_id;
            $versionsCount = TariffList::where('data_id', $dataId)->count();

            ExpenseRange::where('tariff_list_id', $tariffList->tariff_list_id)->delete();
            $tariffList->delete();

            if ($versionsCount === 1) {
                Data::where('data_id', $dataId)->delete();
            }
        });
    }
}
