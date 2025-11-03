<?php

namespace App\Actions\TariffList;

use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;
use Illuminate\Support\Facades\DB;

class GetGroupedTariffVersions
{
    public function execute(): array
    {
        $tariffListsQuery = TariffList::with('data')
            ->select('data_id', DB::raw('MAX(effectivity_date) as latest_date'))
            ->groupBy('data_id')
            ->orderBy('latest_date', 'desc')
            ->get();

        $groupedTariffs = [];
        $tariffModels = [];

        foreach ($tariffListsQuery as $list) {
            $tariffModel = TariffList::where('data_id', $list->data_id)
                ->orderBy('effectivity_date', 'desc')
                ->orderBy('tariff_list_id', 'desc')
                ->first();

            $tariffModels[$list->data_id] = $tariffModel;

            $servicesList = ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)
                ->join('services', 'expense_ranges.service_id', '=', 'services.service_id')
                ->pluck('services.service')
                ->unique()
                ->toArray();

            $groupedTariffs[$list->data_id] = [
                'tariff_list_id' => $tariffModel->tariff_list_id,
                'services' => $servicesList,
            ];
        }

        return [
            'tariffModels' => $tariffModels,
            'groupedTariffs' => $groupedTariffs,
        ];
    }
}
